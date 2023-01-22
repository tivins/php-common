#!/usr/bin/env php
<?php

use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser\Php7;
use PhpParser\PrettyPrinter\Standard;
use Tivins\Core\Proc\AsciiProcess;
use Tivins\Core\Proc\Command;
use Tivins\Core\StrUtil;
use Tivins\Core\System\File;
use Tivins\Core\System\FileSys;

require 'vendor/autoload.php';

$it = FileSys::getIterator('docs/src');
foreach ($it as $file) {
    if ($file->isDir()) continue;
    if ($file->getExtension() != 'md') continue;
    cliConvertMarkdown($file->getPathName());
}

function cliConvertMarkdown(string $inFile): void
{
    echo "Converting $inFile...\n";
    try {
        convertMarkdown($inFile);
    } catch (Exception $ex) {
        echo ">> " . $ex->getMessage() . "\n";
    }
}

function highPHP(string $code): string
{
    return (new \Tivins\Dev\PHPHighlight)->highlight($code);
}

function convertAscii(array $matches): string
{
    $nodeCode = ($matches[3] ?? '') == 'noCode';
    $phpFile = getcwd() . '/' . $matches[1];
    $outFile = getcwd() . '/' . str_replace('.php', '.cast', $matches[1]);
    $phpCode = File::load($phpFile);
    $output  = File::load($outFile);
    //
    $cast = AsciiProcess::buildCast(new Command('php', $phpFile));

    File::save($outFile, $cast);
    File::save('docs/build/php-common/casts/'.basename($outFile), $cast);

    return ($nodeCode ? '' : highPHP($phpCode))
        . '<div class="asciinema" data-cast="/php-common/casts/' . basename($outFile) . '"></div>'."\n\n";
}


function convertCode($matches): string
{
    $phpFile = getcwd() . '/' . $matches[1];
    $outFile = getcwd() . '/' . str_replace('.php', '.out', $matches[1]);
    $phpCode = File::load($phpFile);
    $output  = File::load($outFile);
    if (!$output) {
        $output = StrUtil::hideIPs(shell_exec('php ' . $phpFile));
    }
    File::save($outFile, $output);
    return highPHP($phpCode)
        . "<details><summary>Output</summary>\n\n<pre>"
        . "\n$output\n</pre>\n"
        . "</details>\n\n";
}

/**
 * @throws Exception
 */
function convertMarkdown(string $inFile): void
{
    $outFile = str_replace('/src/', '/build/php-common/', $inFile);
    $content = File::load($inFile);
    if (!$content) {
        throw new Exception('no-content');
    }
    $content = preg_replace_callback('~{{{ run \| (.*?) \| (.*?) }}}~', function(array $matches): string {
        $file = $matches[1];
        $flags = explode(',', $matches[2]);

        $wantCinema = in_array('cinema', $flags);
        $wantOutput = in_array('output', $flags);

        $phpFile = getcwd() . '/' . $file;
        $outFile = str_replace('.php', '.cast', $file);
        $rawFile = str_replace('.php', '.out', $file);
        $phpCode = File::load($phpFile);

        if (($wantOutput && !File::isReadable($rawFile))
            || ($wantCinema && !File::isReadable($outFile)))
        {
            $process = new AsciiProcess();
            $process->run(new Command('php', $phpFile), 10);
            File::save($outFile, $process->getCast());
            File::save($rawFile, StrUtil::hideIPs($process->getRaw()));
            File::save('docs/build/php-common/casts/'.basename($outFile), $process->getCast());
        }
        $raw = File::load($rawFile);

        $output = '';
        if (in_array('code', $flags)) {
            $output .= highPHP($phpCode);
        }
        if (in_array('output', $flags)) {
            $output .= "<details><summary>Output</summary>\n\n<pre>"
              . "\n". $raw . "\n</pre>\n"
              . "</details>\n\n";
        }
        if ($wantCinema) {
            $output .= '<div class="asciinema" data-cast="/php-common/casts/' . basename($outFile) . '"></div>'."\n\n";
        }
        return $output;

    }, $content);
    // $content = preg_replace_callback('~{{{ runCinema \| (.*?)( \| (.*?))? }}}~', 'convertAscii', $content);
    // $content = preg_replace_callback('~{{{ run \| (.*?) }}}~', 'convertCode', $content);

    $tpl = File::load('docs/src/template.html');
    $tpl = str_replace('{{ HTML }}', StrUtil::markdown($content), $tpl);
    File::save(str_replace('.md','.html', $outFile), $tpl);
}
