<?php

namespace Tivins\Core;


/**
 * Simple Template Engine
 *
 * Basics replacements :
 *
 * * {{ variable }} : HTML entities.
 * * {$ variable $} : Translated and HTML entities.
 * * {! variable !} : No process.
 * * {^ file.html ^} : Include file.
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
 * <!-- BEGIN blockname -->
 * <p>A block that contains a {{ variable }}.</p>
 * <!-- END blockname -->
 * ```
 *
 * ```php
 * $tpl->block('blockname', ['variable' => 'lorem']);
 * $tpl->block('blockname', ['variable' => 'ipsum']);
 * ```
 *
 * Will render :
 *
 * ```html
 * <p>A block that contains a lorem.</p>
 * <p>A block that contains a ipsum.</p>
 * ```
 *
 * NB: if there is no $tpl->block('blockname', $args), the block will not be
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
 * ...todo
 *
 */
class Tpl
{
    /**
     * @var string
     * @todo Rename to 'content' or 'body'.
     */
    public string $html = '';

    public array $vars = [];

    private array $storage = []; /*blocks*/

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
        $this->html = $this->parseBlocks($this->html);
        return $this;
    }

    /**
     *
     */
    public function parseBlocks(string $str): string
    {
        while (preg_match('~<!-- BEGIN ([a-zA-Z0-9]+) -->~', $str, $matches, PREG_OFFSET_CAPTURE)) {
            [$name, $pos] = $matches[1];

            /** find END block */
            $endTag    = '<!-- END ' . $name . ' -->';
            $endTagPos = strpos($str, $endTag, $pos);

            $startTagPos   = $pos + strlen($name) + strlen(' -->');
            $blockStartPos = $pos - strlen('<!-- BEGIN ');
            $blockEndPos   = $endTagPos + strlen($endTag);

            /** get content strings */
            $inside = substr($str, $startTagPos, $endTagPos - $startTagPos);
            $block  = substr($str, $blockStartPos, $blockEndPos - $blockStartPos);

            $str = substr($str, 0, $blockStartPos)
                . '<!-- tpl(' . sha1($name) . ') -->'
                . substr($str, $blockEndPos);

            /** store block */
            $this->storage[$name] = [
                'data' => $inside,
                'processed' => '',
            ];
        }
        return $str;
    }

    /**
     *
     */
    public static function fromFile(string $filename, bool $addIncludeDir = false): self
    {
        $tpl = new self(file_get_contents($filename));
        if ($addIncludeDir) {
            $tpl->addIncludeDirectory(dirname($filename));
        }
        return $tpl;
    }

    public function addIncludeDirectory(string $directory): void
    {
        $this->includeDirs[] = $directory;
    }

    public function addFunction($name, callable $callback)
    {
        $this->allowedFunctions[$name] = $callback;
    }

    /**
     *
     */
    public function concat(string $html): self
    {
        $this->html .= $html;
        return $this;
    }

    /**
     *
     */
    public function loadFile(string $filename): bool
    {
        $data = $this->loadTemplate($filename);
        if ($data === false) return false;
        $this->setBody($data);
        return true;
    }

    /**
     *
     */
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

    /**
     *
     */
    public function setVar(string $key, string $value): self
    {
        $this->vars[$key] = $value;
        return $this;
    }

    /**
     *
     */
    public function block(string $name, array $data)
    {
        if (!isset($this->storage[$name])) {
            // $this->storage[$name]['processed'] .= '*miss*';
            return;
        }
        $tpl = new Tpl($this->storage[$name]['data']);
        $tpl->setVars($data);
        $this->storage[$name]['processed'] .= $tpl;
    }

    /**
     *
     */
    public function setVars(array $keys_values): self
    {
        $this->vars += $keys_values;
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
    public function process(string $str, array $vars): string
    {
        $str = preg_replace_callback('~{\^\s?([a-zA-Z0-9\-.]*)\s?\^}~',
            fn($matches) => $this->loadTemplate($matches[1]),
            $str
        );

        $str = $this->replaceBlocks($str);

        $str = preg_replace_callback('~{{\s?([a-zA-Z0-9]*)\s?\|?\s?([a-zA-Z0-9_,]+)?\s?}}~',
            function ($matches) use ($vars) {
                $base = $vars[$matches[1]] ?? $matches[1];
                if (isset($matches[2]) && isset($this->allowedFunctions[$matches[2]])) {
                    $base = call_user_func($this->allowedFunctions[$matches[2]], $base);
                }
                return StringUtil::html($base);
            },
            $str
        );

        $str = preg_replace_callback('~{!\s?(.*)\s?!}~U',
            fn($matches) => ($vars[$matches[1]] ?? $matches[1]),
            $str
        );

        /**
         * @todo Implement translation
         */
        // $str = preg_replace_callback('~{\$\s?(.*)\s?\$}~U',
        //     fn($matches) => StringUtil::html(I18n::get($vars[$matches[1]] ?? $matches[1])),
        //     $str);

        return $str;
    }

    /**
     *
     */
    public function replaceBlocks(string $str): string
    {
        foreach ($this->storage as $name => $data) {
            $str = str_replace('<!-- tpl(' . sha1($name) . ') -->', $data['processed'], $str);
        }
        return $str;
    }
}
