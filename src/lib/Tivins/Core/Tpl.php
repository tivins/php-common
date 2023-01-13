<?php

namespace Tivins\Core;


use Tivins\Core\Intl\Intl;

/**
 * Simple Template Engine
 *
 * Basics replacements :
 *
 * - `{{ variable }}` : HTML entities.
 * - `{$ variable $}` : Translated and HTML entities.
 * - `{! variable !}` : No process.
 * - `{^ file.html ^}` : Include file.
 *
 * ## Usage
 *
 * ```php
 * use Tivins\Framework\Tpl;
 * $tpl = new Tpl("<p>{{greetings}}</p>");
 * $tpl->setVar("greetings", "Hello & world");
 * echo $tpl; // `<p>Hello &amp; world</p>`
 * ```
 *
 * ## Blocks
 *
 * ```html
 * <!-- BEGIN blockName -->
 * <p>A block that contains a {{ variable }}.</p>
 * <!-- END blockName -->
 * ```
 *
 * ```php
 * $tpl->block('blockName', ['variable' => 'lorem']);
 * $tpl->block('blockName', ['variable' => 'ipsum']);
 * ```
 *
 * Will render :
 *
 * ```html
 * <p>A block that contains a lorem.</p>
 * <p>A block that contains a ipsum.</p>
 * ```
 *
 * NB: if there is no $tpl->block('blockName', $args), the block will not be
 * rendered.
 *
 * ## Nested blocks
 *
 * ```html
 * <!-- BEGIN block_name -->
 * <p>A block that contains a {{ variable }}.</p>
 *   <!-- BEGIN sub_block_name -->
 *   <p>A sub block that contains {{  anotherVariable }}.</p>
 *   <!-- END sub_block_name -->
 * <!-- END block_name -->
 * ```
 *
 * ```php
 * $tpl->getSubTpl('block_name')?->block('sub_block_name', ['anotherVariable' => 'foo']);
 * $tpl->getSubTpl('block_name')?->block('sub_block_name', ['anotherVariable' => 'foo']);
 * $tpl->block('sub_block_name', ['anotherVariable' => 'foo']);
 * ```
 *
 */
class Tpl
{
    public string $html = '';

    public array $vars = [];

    private array $storage = []; /*blocks*/

    public function getStorage(): array { return $this->storage; }
    public function getRawHTML(): string { return $this->html; }

    /**
     * @var string[]
     */
    private array $includeDirs = [];

    /**
     * @var string[]
     */
    private array $allowedFunctions = [
        'ucfirst' => 'ucfirst',
        'lowercase' => 'mb_strtolower',
        'uppercase' => 'mb_strtoupper',
        'number_format' => 'number_format',
        'round' => 'round',
        'floor' => 'floor',
        'abs' => 'abs',
    ];


    /**
     *
     */
    public function __construct(string $body = '')
    {
        $this->setBody($body);
    }

    /**
     *
     */
    public function setBody(string $body): self
    {
        $this->html = $body;
        $this->processIncludes();
        $this->html = $this->parseBlocks($this->html);
        return $this;
    }


    /**
     *
     */
    public static function fromFile(string $filename, bool $addIncludeDir = false): self
    {
        $tpl = new self();
        if ($addIncludeDir) {
            $tpl->addIncludeDirectory(dirname($filename));
        }
        $tpl->setBody(file_get_contents($filename));
        return $tpl;
    }

    public function addIncludeDirectory(string $directory): void
    {
        $this->includeDirs[] = $directory;
    }

    public function addFunction($name, callable $callback): void
    {
        $this->allowedFunctions[$name] = $callback;
    }

    public function concat(string $html): self
    {
        $this->html .= $html;
        return $this;
    }

    public function loadFile(string $filename): bool
    {
        $data = $this->loadTemplate($filename);
        if ($data === false) return false;
        $this->setBody($data);
        return true;
    }

    private function loadTemplate(string $filename): string|false
    {
        foreach ($this->includeDirs as $dir) {
            $filename = $dir . '/' . $filename;
            if (file_exists($filename)) {
                return file_get_contents($filename);
            }
        }
        return false;
    }

    public function setVar(string $key, string $value): self
    {
        $this->vars[$key] = $value;
        return $this;
    }

    public function reset(string $body): void {
        $this->vars=[];
        $this->storage=[];
        $this->setBody($body);
    }
    /**
     *
     */
    public function parseBlocks(string $str): string
    {
        while (preg_match('~<!-- BEGIN ([a-zA-Z0-9_]+) -->~', $str, $matches, PREG_OFFSET_CAPTURE)) {
            /** @var int $pos */
            [$name, $pos] = $matches[1];

            /** find END block */
            $endTag    = '<!-- END ' . $name . ' -->';
            $endTagPos = strpos($str, $endTag, $pos);

            $startTagPos   = $pos + strlen($name) + strlen(' -->');
            $blockStartPos = $pos - strlen('<!-- BEGIN ');
            $blockEndPos   = $endTagPos + strlen($endTag);

            /** get content strings */
            $inside = substr($str, $startTagPos, $endTagPos - $startTagPos);
            # $block  = substr($str, $blockStartPos, $blockEndPos - $blockStartPos);

            $str = substr($str, 0, $blockStartPos)
                . '<!-- tpl(' . sha1($name) . ') -->'
                . substr($str, $blockEndPos);

            /** store block */
            $this->storage[$name] = [
                'data'       => $inside,
                'processed'  => '',
                'tpl'        => str_contains($inside, '<!-- BEGIN') ? new Tpl($inside) : null,
            ];
        }
        return $str;
    }

    /**
     *
     */
    public function getSubTpl(string $blockName): ?Tpl
    {
        return $this->storage[$blockName]['tpl'] ?? null;
    }

    public function block(string $name, array $data = []): static
    {
        if (!isset($this->storage[$name])) {
            return $this;
        }
        if ($this->storage[$name]['tpl']) {
            $this->storage[$name]['processed'] .= $this->storage[$name]['tpl'];
            $this->storage[$name]['tpl']->reset($this->storage[$name]['data']);
            return $this;
        }
        $tpl = new Tpl($this->storage[$name]['data']);
        //var_dump("SubTplStorate=", $tpl->getStorage());
        if (!empty($data)) $tpl->setVars($data);
        $this->storage[$name]['processed'] .= $tpl;
        return $this;
    }

    /**
     *
     */
    public function setVars(array $keys_values): self
    {
        $this->vars = array_merge($this->vars, $keys_values);
        return $this;
    }

    /**
     *
     */
    public function __toString(): string
    {
        return $this->process($this->html, $this->vars);
    }

    /**
     *
     */
    public function processIncludes(): void {
        $this->html = preg_replace_callback('~{\^\s?([_a-zA-Z0-9\-.]*)\s?\^}~',
            fn($matches) => $this->loadTemplate($matches[1]),
            $this->html
        );
    }

    public function process(string $str, array $vars): string
    {
        $str = $this->replaceBlocks($str);

        $str = preg_replace_callback('~{{\s?(.*?)\s?\|?\s?([a-zA-Z0-9_,]+)?\s?}}~',
            function ($matches) use ($vars) {
                $matches = array_values(array_filter($matches));
                $base = $vars[$matches[1]] ?? $matches[1];
                $encode = true;
                if (isset($matches[2]) && isset($this->allowedFunctions[$matches[2]])) {
                    $base = call_user_func_array($this->allowedFunctions[$matches[2]], [$base]);
                }
                /** @todo */
                return $encode ? StrUtil::html($base) : $base;
            },
            $str
        );

        $str = preg_replace_callback('~{!\s*(.*?)\s*!}~',
            fn($matches) => ($vars[$matches[1]] ?? ''),
            $str
        );

        $str = preg_replace_callback('~{\$\s*(.*?)\s*\$}~',
            fn($matches) => StrUtil::html(Intl::get($matches[1])),
            $str);

        return $str;
    }

    /**
     * Replace stored processed blocks in the given string.
     * @see storage
     */
    private function replaceBlocks(string $str): string
    {
        foreach ($this->storage as $name => $data) {
            $str = str_replace('<!-- tpl(' . sha1($name) . ') -->', $data['processed'], $str);
        }
        return $str;
    }
}
