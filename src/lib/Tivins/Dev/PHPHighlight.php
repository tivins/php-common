<?php

namespace Tivins\Dev;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Exit_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\PostDec;
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Expr\PreDec;
use PhpParser\Node\Expr\PreInc;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Scalar\EncapsedStringPart;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\Throw_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NodeConnectingVisitor;
use PhpParser\ParserFactory;
use Tivins\Core\StrUtil;
use Tivins\Core\System\File;

class PHPHighlight
{
    protected int $level = 0;
    protected bool $includeOpenTag = false;
    protected string $propertyFetchSeparator = '->';
    protected string $classConstFetchSeparator = '::';

    public function highlight(string $code): string
    {
        $lexer = new \PhpParser\Lexer(array(
            'usedAttributes' => array('comments', 'startLine', 'endLine', 'startFilePos', 'endFilePos'),
        ));
        $traverser = new NodeTraverser;
        $traverser->addVisitor(new NodeConnectingVisitor);
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7, $lexer);
        $stmts  = $traverser->traverse($parser->parse($code));
        //
        $html = '';
        if ($this->includeOpenTag) {
            $html .= $this->getSpan('<?php', 'cp') . "\n";
        }
        $html .= $this->parseStmts($stmts);
        File::save('/tmp/phpl.php', strip_tags($html));
        $id = 'code-'.substr(sha1($code), 0, 8);
        return "\n"
                . '<pre class="highlight">'
                    . '<a href="#" class="clipper" data-target="#'.$id.'">COPY</a>'
                    . '<div id="'.$id.'">'
                    . $html
                    . '</div>'
                . '</pre>'
            // . '<details><summary>Original code</summary>'
            // . '<pre>'.shell_exec('diff /tmp/phpl.php' . $code).'</pre>'
            // . '<pre>'.htmlentities($code).'</pre>'
            // . '<pre>'.strip_tags($html).'</pre>'
            // . '<pre>'.var_export(shell_exec('cat /tmp/phpl.php'),true).'</pre>'
            // . '<pre>'.var_export(shell_exec('php -l /tmp/phpl.php'),true).'</pre>'
            // . '</details>'
            . "\n";
    }

    /**
     * @param array $stmts
     * @return string
     */
    protected function parseStmts(array $stmts): string {
        $html = '';
        foreach ($stmts as $k => $node) {
            if ($node instanceof GroupUse) {
                $html .= $this->parseUse_($node);
            }
            elseif ($node instanceof Use_) {
                $html .= $this->parseUse_($node);
                if (!($stmts[$k+1] instanceof Use_)) {
                    $html .= "\n";
                }
            }
            elseif ($node instanceof Echo_) {
                $elements = [];
                foreach ($node->exprs as $expr) {
                    $elements[] = $this->parseNodeExpr($expr);
                }
                $html .= $this->getIndentLine(
                    $this->getSpan('echo', 'k') . ' '
                    . join($this->getSpan(' . ', 'p'), $elements)
                    . $this->getSpanPunctuation(';')
                );
            }
            elseif ($node instanceof Throw_) {
                $html .= $this->parseThrow($node);
            }
            elseif ($node instanceof If_) {
                $html .= $this->parseIf($node);
            }
            elseif ($node instanceof Class_) {
                $html .= $this->parseClass($node);
            }
            elseif ($node instanceof Trait_) {
                $html .= $this->parseTrait($node);
            }
            elseif ($node instanceof TryCatch) {
                $html .= $this->parseTryCatch($node);
            }
            elseif ($node instanceof Function_) {
                $html .= $this->parseFunction($node);
            }
            elseif ($node instanceof Foreach_) {
                $html .= $this->parseForeach($node);
            }
            elseif ($node instanceof Expression) {
                $html .= $this->parseExpression($node);
            }
            elseif ($node instanceof Return_) {
                $html .= $this->getIndentLine(
                    $this->getSpan('return', 'k')
                    . ($node->expr ?  ' ' . $this->parseNodeExpr($node->expr) : '')
                    . $this->getSpanPunctuation(';')
                );
            }
            // \PhpParser\Node\Stmt\If_
            // \PhpParser\Node\Stmt\Return_
            else {
                echo 'NotImplemented ['.__function__.'] "'.$node::class.'"'."\n";
                $html .= $this->getIndentLine('// ** ' . __line__ . $node::class . ";");
            }
        }
        return $html;
    }

    protected function getIndentLine(string $line): string
    {
        return $this->getIndent() . $line . "\n";
    }

    protected function getIndent(): string
    {
        return str_repeat(' ', 4 * $this->level);
    }

    protected function getSpanPunctuation(string $content): string
    {
        return $this->getSpan($content, 'p');
    }

    protected function getSpan(string $content, string $class, bool $escape = true): string
    {
        return '<span class="' . $class . '">' . ($escape ? htmlentities($content) : $content) . '</span>';
    }

    protected function parseUse(UseUse $use): string
    {
        $prefix = isset($node->prefix) ? $node->prefix . '\\' : '';
        $defaultAlias = substr($use->name,strrpos($use->name, '\\')+1);
        return $this->getIndentLine(
            $this->getSpan('use', 'kn')
            . ' '
            . $this->getSpan($prefix . $use->name, 'nn')
            . ($defaultAlias != $use->getAlias()->name
                ? ' '
                . $this->getSpan('as', 'kn')
                . ' '
                . $this->getSpan($use->getAlias()->name, 'nn')
                : ''
            )
            . $this->getSpanPunctuation(';')
        );
    }
    protected function parseUse_(GroupUse|Use_ $node): string
    {
        $html = '';
        foreach ($node->uses as $use) {
            $html .= $this->parseUse($use);
        }
        return $html;
    }

    protected function parseExpression(Expression $node): string
    {
        return $this->getIndentLine(
            $this->parseNodeExpr($node->expr)
            . $this->getSpanPunctuation(';')
            . ($node->expr::class == Include_::class ? "\n" : "")
            );
    }

    protected function parseNodeExpr(Expr $expr): string
    {
        $class = $expr::class;
        switch ($class) {
            case DNumber::class:
            case LNumber::class:
                return $this->getSpan($expr->value, 'mi');
            case Encapsed::class:
                return $this->parseEncapsed($expr);
            case EncapsedStringPart::class:
                return $this->parseEncapsedStringPart($expr);
            case String_::class:
                return $this->parseString($expr);
            case FuncCall::class:
                return $this->parseFunCall($expr);
            case MethodCall::class:
                return $this->parseMethodCall($expr);
            case StaticCall::class:
                return $this->parseStaticCall($expr);
            case Include_::class:
                return $this->parseInclude($expr);
            case ConstFetch::class:
                return $this->getSpan($expr->name, 'kc');
            case Closure::class:
                return $this->parseClosure($expr);
            case ClassConstFetch::class:
                return $this->parseClassConstFetch($expr);
            case StaticPropertyFetch::class:
            case PropertyFetch::class:
                return $this->parsePropertyFetch($expr);
            case Variable::class:
                return $this->parseVar($expr);
            case New_::class:
                return $this->parseNew($expr);
            case Exit_::class:
                return $this->parseExit($expr);
            case Array_::class:
                return $this->parseArray($expr);
            case Expr\BinaryOp\Minus::class:
            case Expr\BinaryOp\Plus::class:
            case Expr\BinaryOp\Mod::class:
            case Expr\BinaryOp\Mul::class:
            case Expr\BinaryOp\Concat::class:
            case Expr\BinaryOp\BooleanAnd::class:
            case Expr\BinaryOp\BooleanOr::class:
            case Expr\BinaryOp\Equal::class:
            case Expr\BinaryOp\NotEqual::class:
            case Expr\BinaryOp\Smaller::class:
            case Expr\BinaryOp\SmallerOrEqual::class:
            case Expr\BinaryOp\Greater::class:
            case Expr\BinaryOp\GreaterOrEqual::class:
            case Expr\BinaryOp\Div::class:
                return $this->parseBinaryOp($expr);

            case Expr\AssignOp\Plus::class:
            case Expr\AssignOp\Minus::class:
            case Expr\AssignOp\Mul::class:
            case Expr\AssignOp\Div::class:
            case Expr\AssignOp\Mod::class:
            case Expr\AssignOp\Pow::class:
            case Expr\AssignOp\Concat::class:
            case Expr\AssignOp\Coalesce::class:
            case Expr\AssignOp\ShiftRight::class:
            case Expr\AssignOp\ShiftLeft::class:
            case Expr\AssignOp\BitwiseAnd::class:
            case Expr\AssignOp\BitwiseOr::class:
            case Expr\AssignOp\BitwiseXor::class:
            case Assign::class:
                return $this->parseAssign($expr);

            case ArrayDimFetch::class:
                return $this->parseArrayDimFetch($expr);
            case PostInc::class: return $this->parsePostInc($expr, true);
            case PostDec::class: return $this->parsePostDec($expr, true);
            case PreInc::class: return $this->parsePostInc($expr, false);
            case PreDec::class: return $this->parsePostDec($expr, false);
            case BooleanNot::class:
                return '!'.$this->parseNodeExpr($expr->expr);
            default:
                echo 'ERR 98787 match not found ('.$class.')'."\n";
                return 'ERR 98787 match not found ('.$class.')';
        }
    }

    public function parseBinaryOp(Expr\BinaryOp $expr): string
    {
        $p = $this->getSpanPunctuation('(');
        $f = $this->getSpanPunctuation(')');
        if (in_array($expr->getOperatorSigil(), ['.', '*', '/', '&&'])) {
            $p = $f = '';
        }
        return
            $p
            . $this->parseNodeExpr($expr->left)
            . ' '
            . $this->getSpan($expr->getOperatorSigil(), 'mf')
            . ' '
            . $this->parseNodeExpr($expr->right)
            . $f;
    }

    protected function parseInclude(Include_ $expr): string
    {
        return $this->getSpan(match ($expr->type) {
            Include_::TYPE_INCLUDE => 'include',
            Include_::TYPE_INCLUDE_ONCE => 'include_once',
            Include_::TYPE_REQUIRE => 'require',
            Include_::TYPE_REQUIRE_ONCE => 'require_once',
        }, 'k')
        . ' '
        . $this->parseNodeExpr($expr->expr);
    }

    protected function parseStaticCall(StaticCall $expr): string
    {
        $computedArgs = [];
        foreach ($expr->args as $arg) {
            $computedArgs[] = $this->parseNodeExpr($arg->value);
        }
        $html = $this->getSpan($expr->class, 'nc');
        $html .= $this->getSpan($this->classConstFetchSeparator, 'o');
        $html .= $this->getSpan($expr->name, 'nf');
        $html .= '('.join(', ', $computedArgs).')';
        return $html;
    }
    protected function parseNew(New_ $expr): string
    {
        $computedArgs = [];
        foreach ($expr->args as $arg) {
            $computedArgs[] = $this->parseNodeExpr($arg->value);
        }

        // -- Check: '(new Foo)->call()'
        // -- or: '$a = new Foo;'
        /** @var Node $node */
        $node = $expr->getAttribute('parent');
        $p = $this->getSpanPunctuation('(');
        $f = $this->getSpanPunctuation(')');
        if ($node->getType() != 'Expr_MethodCall') {
            $p = $f = '';
        }

        return $p
            . $this->getSpan('new', 'k')
            . ' '
            . $this->getSpan($expr->class, 'nc')
            . $this->getSpanPunctuation('(')
            . join(', ', $computedArgs)
            . $this->getSpanPunctuation(')')
            . $f;
    }
    protected function parseMethodCall(MethodCall $expr): string
    {
        $computedArgs = [];
        foreach ($expr->args as $arg) {
            $computedArgs[] = $this->parseNodeExpr($arg->value);
        }
        $html = '';
        if ($expr->getDocComment()) {
            $html .= $this->getIndentLine(
                $this->getSpan($expr->getDocComment()->getText(), 'c1')
            );
        }
        $html .= $this->parseNodeExpr($expr->var);
        $html .= $this->getSpan($this->propertyFetchSeparator, 'o');
        $html .= $this->getSpan($expr->name, 'nf');
        $html .= '('.join(', ', $computedArgs).')';
        return $html;

    }
    protected function parseFunCall(FuncCall $expr): string
    {
        $computedArgs = [];
        foreach ($expr->args as $arg) {
            $computedArgs[] = ($arg->name ? $this->getSpan($arg->name.': ','s') : '')
                . $this->parseNodeExpr($arg->value);
        }
        return $this->getSpan($expr->name, 'nb')
            . $this->getSpanPunctuation('(')
            . join($this->getSpanPunctuation(', '), $computedArgs)
            . $this->getSpanPunctuation(')');
    }

    protected function parseClosure(Closure $expr): string
    {
        echo " !! ".__function__."() is incomplete\n";
        $this->level++;
        $html = "\n"
            . $this->getIndentLine(
                $this->getSpan('function', 'k')
                . ' (' . '*todo*' . ') '
                . 'use' . ' (' . '*todo*' . ') {'
            )
        ;
        $this->level++;
        $html .= $this->parseStmts($expr->stmts);
        $this->level--;
        $html .= $this->getIndentLine($this->getSpanPunctuation('}'));
        $this->level--;
        return $html;
    }

    protected function parseTryCatch(TryCatch $node): string
    {
        $html = $this->getIndentLine($this->getSpan('try', 'k').$this->getSpanPunctuation(' {'));
        $this->level++;
        $html .= $this->parseStmts($node->stmts);
        $this->level--;
        $html .= $this->getIndentLine($this->getSpanPunctuation('}'));
        foreach ($node->catches as $catch) {
            $html .= $this->getIndentLine($this->getSpan('catch', 'k')
                . ' ' . $this->getSpanPunctuation('(')
                . join('|', $catch->types)
                . ' '
                . $this->parseNodeExpr($catch->var)
                . $this->getSpanPunctuation(')')
                . ' '
                . $this->getSpanPunctuation('{')
            );
            $this->level++;
            $html .= $this->parseStmts($catch->stmts);
            $this->level--;
            $html .= $this->getIndentLine($this->getSpanPunctuation('}'));
        }
        if ($node->finally) {
            $html .= $this->getIndentLine($this->getSpan('finally', 'k').$this->getSpan(' {', 'p'));
            $this->level++;
            $html .= $this->parseStmts($node->finally->stmts);
            $this->level--;
            $html .= $this->getIndentLine($this->getSpan('}','p'));
        }
        return $html;
    }

    protected function parseForeach(Foreach_ $node): string
    {
        echo ' -> ' . __function__ . "() is incomplete\n";
        $html = $this->getIndentLine('foreach (*todo*) {');
        $this->level++;
        $html .= $this->parseStmts($node->stmts);
        $this->level--;
        $html .= $this->getIndentLine('}');
        return $html;
    }

    /**
     * @param Expr $expr
     * @return string
     */
    protected function parseString(Expr $expr): string
    {
        [$chr, $class] = match ($expr->getAttribute('kind')) {
            String_::KIND_DOUBLE_QUOTED => ['"', 's2'],
            String_::KIND_SINGLE_QUOTED => ["'", 's1'],
        };
        return $this->getSpan(StrUtil::escape($expr->value, $chr), $class);
    }

    protected function parseAssign(Assign|AssignOp $expr): string
    {
        $opRef = [
            'Expr_AssignOp_Plus'     => '+',
            'Expr_AssignOp_Minus'    => '-',
            'Expr_AssignOp_Mul'      => '*',
            'Expr_AssignOp_Div'      => '/',
            'Expr_AssignOp_Mod'      => '%',
            'Expr_AssignOp_Concat'   => '.',
            'Expr_AssignOp_Coalesce' => '??',
            'Expr_AssignOp_BitwiseXor' => '^',
            'Expr_AssignOp_BitwiseAnd' => '&',
            'Expr_AssignOp_BitwiseOr' => '|',
            'Expr_AssignOp_ShiftRight' => '>>',
            'Expr_AssignOp_ShiftLeft' => '<<',
        ];

        $op = '';
        if ($expr instanceof AssignOp) {
            $op = $opRef[$expr->getType()];
        }
        return $this->parseNodeExpr($expr->var)
            . ' '
            . "$op="
            . ' '
            . $this->parseNodeExpr($expr->expr);
    }

    protected function parseArray(Array_ $expr): string
    {
        return '['
            .join(', ', array_map(function(Expr\ArrayItem $item) {
                return ($item->key ? $this->parseNodeExpr($item->key) . ' ' .$this->getSpan('=>', 'o') . ' ' : '') . $this->parseNodeExpr($item->value);
            }, $expr->items))
            .']';
    }

    protected function parseExit(Exit_ $expr): string
    {
        return $this->getSpan('exit', 'k')
            . $this->getSpan('(', 'p')
            . $this->parseNodeExpr($expr->expr)
            . $this->getSpan(')', 'p');
    }

    protected function parsePropertyFetch(StaticPropertyFetch|PropertyFetch $expr): string
    {
        $sep = $this->propertyFetchSeparator;
        $left = '';
        if ($expr instanceof StaticPropertyFetch) {
            $sep = $this->classConstFetchSeparator;
            $left = $expr->class;
        }
        else{
            $left = $this->parseNodeExpr($expr->var);
        }

        return
            $left
            . $this->getSpan($sep, 'o')
            . $this->getSpan($expr->name, 'n');
    }

    protected function parseClassConstFetch(ClassConstFetch $expr): string
    {
        $html = $expr->class;
        $html .= $this->getSpan($this->classConstFetchSeparator, 'o');
        $html .= $this->getSpan($expr->name, 'n');
        return $html;
    }

    protected function parseEncapsed(Encapsed $expr): string
    {
        return $this->getSpan('"'
            . join(array_map(fn($p) => $this->parseNodeExpr($p), $expr->parts))
            . '"', 's2', false);
    }

    /** @noinspection SpellCheckingInspection */
    protected function parseEncapsedStringPart(EncapsedStringPart $expr): string
    {
        return str_replace("\n", '\n', $expr->value);
    }

    protected function parsePostDec(PreDec|PostDec $expr, bool $post = true): string
    {
        return ($post ? '' : '--') . $this->parseNodeExpr($expr->var) . ($post ? '--' : '');
    }
    protected function parsePostInc(PreInc|PostInc $expr, bool $post = true): string
    {
        return ($post ? '' : '++'). $this->parseNodeExpr($expr->var) . ($post ? '++' : '');
    }

    protected function parseArrayDimFetch(ArrayDimFetch $expr): string
    {
        return $this->parseNodeExpr($expr->var)
            . $this->getSpanPunctuation('[')
            . $this->parseNodeExpr($expr->dim)
            . $this->getSpanPunctuation(']')
            ;
    }

    protected function getAccess(mixed $item): string
    {
        $access = '';
        if (method_exists($item, 'isPublic') && $item->isPublic())
            $access = 'public';
        if (method_exists($item, 'isProtected') && $item->isProtected())
            $access = 'protected';
        if (method_exists($item, 'isPrivate') && $item->isPrivate())
            $access = 'private';

        return $access ? $this->getSpan($access, 'k') : '';
    }

    protected function parseClass(Class_ $node): string
    {
        $sign = $this->getSpan('class', 'kd')
            . ' '
            . $this->getSpan($node->name, 'nc')
            ;
        if ($node->extends) {
            $sign .= ' '
                . $this->getSpan('extends', 'k')
                . ' '
                . $this->getSpan($node->extends, 'nc')
                ;
        }

        // -- output
        $html = $this->getIndentLine($sign);
        $html .= $this->getIndentLine('{');
        $this->level++;
        if (!empty($node->getTraitUses())) {
            foreach ($node->getTraitUses() as $traitUse) {
                $html .= $this->getIndentLine(
                    'use '
                    . join($this->getSpanPunctuation(', '), $traitUse->traits)
                    . $this->getSpanPunctuation(';')
                );
            }
            $html .= "\n";
        }

        $html .= $this->parseConstants($node->getConstants());
        $html .= $this->parseProperties($node->getProperties());
        $html .= $this->parseMethods($node->getMethods());


        $this->level--;
        $html .= $this->getIndentLine('}');
        $html .= "\n";
        return $html;
    }

    protected function parseTrait(Trait_ $node): string
    {
        $sign = 'trait'
            . ' '
            . $node->name
        ;
        $html = $this->getIndentLine($sign);
        $html .= $this->getIndentLine('{');
        $this->level++;
        $html .= $this->parseConstants($node->getConstants());
        $html .= $this->parseMethods($node->getMethods());
        $this->level--;
        $html .= $this->getIndentLine('}');
        $html .= "\n";
        return $html;
    }

    /**
     * @param ClassConst[] $constants
     * @return string
     */
    protected function parseConstants(array $constants): string
    {
        if (empty($constants)) {
            return '';
        }
        $html = '';
        foreach ($constants as $constant) {
            if ($constant->getDocComment()) {
                $html .= $this->getIndentLine(
                    $this->getSpan($constant->getDocComment()->getText(), 'c1')
                );
            }
            $html .= $this->getIndentLine(
                $this->getAccess($constant)
                . ' '
                . 'const'
                . ' '
                . $constant->consts[0]->name
                . ' = '
                . $this->parseNodeExpr($constant->consts[0]->value)
                . ';'
            );
        }
        $html .= "\n";
        return $html;
    }

    protected function parseParams(FunctionLike $method): array {
        return array_map(
            fn(Param $param) => $this->parseParam($param),
            $method->getParams()
        );
    }
    protected function getFunctionLikeSignature(FunctionLike $method): string {

        $pfx = $this->getAccess($method);
        $pfx .= method_exists($method, 'isStatic') && $method->isStatic()
            ? ($pfx ? ' ' : '') . 'static'
            : '';
        $pfx .= ($pfx ? ' ' : '');


        return
            $pfx
            . $this->getSpan('function', 'k')
            . ' '
            . $this->getSpan($method->name, 'n')
            . $this->getSpanPunctuation('(')
            . join($this->getSpanPunctuation(', '), $this->parseParams($method))
            . $this->getSpanPunctuation(')')
            . $this->getSpanPunctuation(': ')
            . $this->getSpan($this->parseType($method->returnType), 'kt')
        ;
    }

    /**
     * @param FunctionLike $method
     * @return string
     */
    protected function parseFunctionLike(FunctionLike $method): string
    {
        $html = '';
        if ($method->getDocComment()) {
            $html .= $this->getIndentLine(
                $this->getSpan($method->getDocComment()->getText(), 'c1')
            );
        }
        $html .= $this->getIndentLine($this->getFunctionLikeSignature($method));
        $html .= $this->getIndentLine($this->getSpanPunctuation('{'));
        $html .= $this->parseIndentedStatements($method->stmts);
        $html .= $this->getIndentLine($this->getSpanPunctuation('}'));
        return $html;
    }

    protected function parseIndentedStatements(array $stmts) {
        $this->level++;
        $html = $this->parseStmts($stmts);
        $this->level--;
        return $html;
    }

    protected function parseFunction(Function_ $node): string
    {
        return $this->parseFunctionLike($node)."\n";
    }

    /**
     * @param ClassMethod[] $methods
     * @return string
     */
    protected function parseMethods(array $methods): string
    {
        if (empty($methods)) {
            return '';
        }
        $html = array_map(fn($m) => $this->parseFunctionLike($m), $methods);
        return join("\n", $html);
    }

    protected function parseIf(If_ $node): string
    {
        $html = $this->getIndentLine(
            $this->getSpan('if', 'k')
            . $this->getSpanPunctuation(' (')
            . $this->parseNodeExpr($node->cond)
            . $this->getSpanPunctuation(') {')
        );
        $this->level++;
        $html .= $this->parseStmts($node->stmts);
        $this->level--;
        $html .= $this->getIndentLine('}');
        if (!empty($node->elseifs)) {
            foreach ($node->elseifs as $elseif) {
                $html .= $this->getIndentLine(
                    $this->getSpan('elseif', 'k')
                    . $this->getSpanPunctuation(' (')
                    . $this->parseNodeExpr($elseif->cond)
                    . $this->getSpanPunctuation(') {')
                );
                $this->level++;
                $html .= $this->parseStmts($elseif->stmts);
                $this->level--;
                $html .= $this->getIndentLine('}');
            }
        }
        if (!empty($node->else)) {

            $html .= $this->getIndentLine(
                $this->getSpan('else', 'k')
                . $this->getSpanPunctuation(' {')
            );
            $this->level++;
            $html .= $this->parseStmts($node->else->stmts);
            $this->level--;
            $html .= $this->getIndentLine('}');
        }
        return $html;
    }

    protected function parseThrow(Throw_ $node): string
    {
        return
            $this->getIndentLine(
                $this->getSpan('throw', 'k').' '.$this->parseNodeExpr($node->expr)
                . $this->getSpanPunctuation(';')
            );
    }

    protected function parseType(Node\Name|Node\Identifier|Node\ComplexType|null $type): string
    {
        if ($type instanceof Node\UnionType) {
            return join('|', $type->types);
        }
        if ($type instanceof Node\NullableType) {
            return '?'.$type->type;
        }
        return (string) $type;
    }

    protected function parseVar(Variable $expr): string
    {
        return $this->getSpan('$' . $expr->name, 'nv');
    }

    /**
     * @param Param $param
     * @return string
     */
    protected function parseParam(Param $param): string
    {
        $pStr = $this->getSpan($this->parseType($param->type), 'kt')
            . ' '
            . $this->parseNodeExpr($param->var);

        if ($param->default) {
            //var_dump('default param = ', $param->default->getType());
            $pStr .= ' = ' . $this->parseNodeExpr($param->default);
        }
        return $pStr;
    }

    /**
     * @param Property[] $getProperties
     * @return string
     */
    protected function parseProperties(array $properties): string
    {
        return join(
            array_map(fn(Property $property) => $this->parseProperty($property), $properties)
        );
    }

    protected  function parseProperty(Property $property): string
    {
        $html = '';
        $html .= $this->getAccess($property);
        $html .= ' ';
        $html .= $property->type;
        $html .= ' $'.$property->props[0]->name;
        $html .= ';';
        return $this->getIndentLine($html);
    }

}