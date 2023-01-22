<?php

namespace Tivins\Dev;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Exit_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Scalar\EncapsedStringPart;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NodeConnectingVisitor;
use PhpParser\ParserFactory;
use Tivins\Core\System\File;

class PHPHighlight
{
    private int $level = 0;

    public function highlight(string $code): string {
        $traverser = new NodeTraverser;
        $traverser->addVisitor(new NodeConnectingVisitor);
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $ast    = $traverser->traverse($parser->parse($code));
        //
        $html = '';//$this->getSpan('<?php', 'cp')."\n";
        $html .= $this->parseStmts($ast);
        File::save('/tmp/phpl.php', strip_tags($html));
        return '<pre class="highlight">'.$html.'</pre>'
            // . '<details><summary>Original code</summary>'
             // . '<pre>'.shell_exec('diff /tmp/phpl.php' . $code).'</pre>'
            // . '<pre>'.htmlentities($code).'</pre>'
            // . '<pre>'.strip_tags($html).'</pre>'
            // . '<pre>'.var_export(shell_exec('cat /tmp/phpl.php'),true).'</pre>'
            // . '<pre>'.var_export(shell_exec('php -l /tmp/phpl.php'),true).'</pre>'
            // . '</details>'
            ."\n";
    }

    /**
     * @param array $stmts
     * @return string
     */
    private function parseStmts(array $stmts): string {
        $html = '';
        foreach ($stmts as $k => $node) {
            if ($node instanceof GroupUse) {

            }
            elseif ($node instanceof Use_) {
                $html .= $this->getIndent().$this->parseUse_($node);
                if (!($stmts[$k+1] instanceof Use_)) {
                    $html .= "\n";
                }
            }
            elseif ($node instanceof Echo_) {
                $elements = [];
                foreach ($node->exprs as $expr) {
                    $elements[] = $this->parseNodeExpr($expr);
                }
                $html .= $this->getIndent().$this->getSpan('echo', 'k') . ' '
                    . join($this->getSpan(' . ', 'p'), $elements)
                    . $this->getSpan(';', 'p')
                    . "\n";

                // $html .= '--'.$node->exprs."\n";
            }
            elseif ($node instanceof TryCatch) {
                $html .= $this->parseTryCatch($node);
            }
            elseif ($node instanceof Foreach_) {
                $html .= $this->parseForeach($node);
            }
            elseif ($node instanceof Expression) {
                $html .= $this->parseExpression($node);
            }
            else {
                echo 'NotImplemented ['.__function__.'] "'.$node::class.'"'."\n";
                $html .= $this->getIndentLine('// ** ' . __line__ . $node::class . ";");
            }
        }
        return $html;
    }

    private function getIndentLine(string $line): string {
        return $this->getIndent().$line."\n";
    }
    private function getIndent(): string
    {
        return str_repeat(' ', 4 * $this->level);
    }
    private function getSpan(string $content, string $class): string
    {
        return '<span class="' . $class . '">' . htmlentities($content) . '</span>';
    }

    private function parseUse_(Use_ $node): string
    {
        $html = '';
        foreach ($node->uses as $use) {
            $defaultAlias = substr($use->name,strrpos($use->name, '\\')+1);
            $html .= $this->getIndentLine(
                $this->getSpan('use', 'kn')
                . ' '
                . $this->getSpan($use->name, 'nn')
                . ($defaultAlias != $use->getAlias()->name ?
                    ' '
                    . $this->getSpan('as', 'kn')
                    . ' '
                    . $this->getSpan($use->getAlias()->name, 'nn')
                    : ''
                )
                . $this->getSpan(';', 'p')
            );
        }
        $html .= '';
        return $html;
    }

    private function parseExpression(Expression $node): string
    {
        return $this->getIndentLine(
            $this->parseNodeExpr($node->expr)
            . $this->getSpan(';', 'p')
            . ($node->expr::class == Include_::class ? "\n" : "")
            );
    }

    private function parseNodeExpr(Expr $expr): string
    {
        // if (!$expr) return '*NUL*';
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
            case PropertyFetch::class:
                return $this->parsePropertyFetch($expr);
            case Variable::class:
                return $this->getSpan('$'.$expr->name, 'nv');
            case New_::class:
                return $this->parseNew($expr);
            case Exit_::class:
                return $this->parseExit($expr);
            case Array_::class:
                return $this->parseArray($expr);
            case Assign::class:
                return $this->parseAssign($expr);
            case ArrayDimFetch::class:
                return $this->parseArrayDimFetch($expr);
            case PostInc::class:
                return $this->parsePostInc($expr);
            case Concat::class:
                return
                    $this->parseNodeExpr($expr->left)
                    .$this->getSpan(' . ', 'mf')
                    .$this->parseNodeExpr($expr->right);
            default:
                echo 'ERR 98787 match not found ('.$class.')'."\n";
                return 'ERR 98787 match not found ('.$class.')';
        }
    }

    private function parseInclude(Include_ $expr): string
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

    private function parseStaticCall(StaticCall $expr): string
    {
        $computedArgs = [];
        foreach ($expr->args as $arg) {
            $computedArgs[] = $this->parseNodeExpr($arg->value);
        }
        $html = $expr->class;
        $html .= $this->getSpan('::', 'o');
        $html .= $expr->name;
        $html .= '('.join(', ', $computedArgs).')';
        return $html;
    }
    private function parseNew(New_ $expr): string
    {
        $computedArgs = [];
        foreach ($expr->args as $arg) {
            $computedArgs[] = $this->parseNodeExpr($arg->value);
        }
        $html = '(new '.$expr->class;
        $html .= '('.join(', ', $computedArgs).'))';
        return $html;
    }
    private function parseMethodCall(MethodCall $expr): string
    {
        $computedArgs = [];
        foreach ($expr->args as $arg) {
            $computedArgs[] = $this->parseNodeExpr($arg->value);
        }
        $html = $this->parseNodeExpr($expr->var);
        $html .= $this->getSpan('->', 'o');
        $html .= $this->getSpan($expr->name, 'nf');
        $html .= '('.join(', ', $computedArgs).')';
        return $html;

    }
    private function parseFunCall(FuncCall $expr): string
    {
        $computedArgs = [];
        foreach ($expr->args as $arg) {
            $computedArgs[] = $this->parseNodeExpr($arg->value);
        }
        $html = $this->getSpan($expr->name, 'nb');
        $html .= '('.join(', ', $computedArgs).')';
        return $html;
    }

    private function parseClosure(Closure $expr): string
    {
        $this->level++;
        $html = "\n".$this->getIndentLine('function() use() {');
        $this->level++;
        $html .= $this->parseStmts($expr->stmts);
        $this->level--;
        $html .= $this->getIndentLine('}');
        $this->level--;
        return $html;
    }


    private function parseTryCatch(TryCatch $node): string
    {
        $html = $this->getIndentLine($this->getSpan('try', 'k').$this->getSpan(' {', 'p'));
        $this->level++;
        $html .= $this->parseStmts($node->stmts);
        $this->level--;
        $html .= $this->getIndentLine($this->getSpan('}','p'));
        foreach ($node->catches as $catch) {
            $html .= $this->getIndentLine($this->getSpan('catch', 'k')
                . ' ('
                . join('|', $catch->types)
                . ' '
                . $this->parseNodeExpr($catch->var)
                . ') {'
            );
            $this->level++;
            $html .= $this->parseStmts($catch->stmts);
            $this->level--;
            $html .= $this->getIndentLine($this->getSpan('}', 'p'));
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

    private function parseForeach(Foreach_ $node): string
    {
        $html = $this->getIndentLine('foreach () {');
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
    private function parseString(Expr $expr): string
    {
        [$chr, $class] = match ($expr->getAttribute('kind')) {
            String_::KIND_DOUBLE_QUOTED => ['"', 's2'],
            String_::KIND_SINGLE_QUOTED => ["'", 's1'],
        };
        return $this->getSpan(
        //$expr->getStartLine().
            $chr . str_replace(["\n", "\t"], ['\n', '\t'], $expr->value) . $chr, $class
        );
    }

    private function parseAssign(Assign $expr): string
    {
        return $this->parseNodeExpr($expr->var) . ' = '.$this->parseNodeExpr($expr->expr);
    }

    private function parseArray(Array_ $expr): string
    {
        return '['
            .join(', ', array_map(function(Expr\ArrayItem $item) {
                return ($item->key ? $this->parseNodeExpr($item->key) . ' ' .$this->getSpan('=>', 'o') . ' ' : '') . $this->parseNodeExpr($item->value);
            }, $expr->items))
            .']';
    }

    private function parseExit(Exit_ $expr): string
    {
        return $this->getSpan('exit', 'k')
            . $this->getSpan('(', 'p')
            . $this->parseNodeExpr($expr->expr)
            . $this->getSpan(')', 'p');
    }

    private function parsePropertyFetch(PropertyFetch $expr): string
    {
        return
            $this->parseNodeExpr($expr->var)
            .'->'
            .$this->getSpan($expr->name,'n');
    }

    private function parseClassConstFetch(ClassConstFetch $expr): string
    {
        $html = $expr->class;
        $html .= $this->getSpan('::', 'o');
        $html .= $expr->name;
        return $html;
    }

    private function parseEncapsed(Encapsed $expr): string
    {
        $html = '"'
            . join(array_map(fn($p) => $this->parseNodeExpr($p), $expr->parts));

        $html .= '"';
        return $html;

    }

    /** @noinspection SpellCheckingInspection */
    private function parseEncapsedStringPart(EncapsedStringPart $expr): string
    {
        return str_replace("\n", '\n', $expr->value);
    }

    private function parsePostInc(PostInc $expr): string
    {
        return "++". $this->parseNodeExpr($expr->var);
    }

    private function parseArrayDimFetch(ArrayDimFetch $expr)
    {
        return $this->parseNodeExpr($expr->var).'['.$this->parseNodeExpr($expr->dim).']';
    }


}