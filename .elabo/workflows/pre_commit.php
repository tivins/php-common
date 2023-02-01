#!/usr/bin/env php
<?php

use Tivins\Core\Proc\AsciiProcess;
use Tivins\Core\Proc\Command;
use Tivins\Core\StrUtil;
use Tivins\Core\System\File;
use Tivins\Core\System\FileSys;
use Tivins\Dev\PHPHighlight;

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
    return (new PHPHighlight)->highlight($code);
}
// https://github.com/tivins/php-common/blob/main/src/lib/Tivins/Core/Proc/AsciiProcess.php
/**
 * @throws Exception
 */
function convertMarkdown(string $inFile): void
{
    $baseTitle = 'php-common';
    $outFile = str_replace('/src/', '/build/php-common/', $inFile);
    $content = File::load($inFile);
    $firstSharp = mb_strpos($content, '#');
    $titleEOL   = mb_strpos($content, "\n", $firstSharp);
    $title      = mb_substr($content, $firstSharp, $titleEOL - $firstSharp);
    $title = $baseTitle.' - ' .trim($title, " \ \t\n\r\0\x0B#");
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
            $output .= (new PHPHighlight)->highlight($phpCode);
        }
        if (in_array('codeJS', $flags)) {
            $output .= (new \Tivins\Dev\PHPJS())->highlight($phpCode);
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
    $tpl = File::load('docs/src/template.html');
    $tpl = str_replace('{{ title }}', $title, $tpl);
    $tpl = str_replace('{{ HTML }}', StrUtil::markdown($content), $tpl);
    File::save(str_replace('.md','.html', $outFile), $tpl);
}
