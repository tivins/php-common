#!/usr/bin/env php
<?php

use Tivins\Core\System\File;
use Tivins\Core\System\FileSys;

require 'vendor/autoload.php';


$it = FileSys::getIterator('docs/src');
foreach ($it as $file) {
    if ($file->isDir()) continue;
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

/**
 * @throws Exception
 */
function convertMarkdown(string $inFile): void
{
    $outFile = str_replace('/src/', '/build/', $inFile);
    $content = File::load($inFile);
    if (!$content) {
        throw new Exception('no-content');
    }
    $content = preg_replace_callback('~{{{ run \| (.*?) }}}~', function ($matches) {
        $phpFile = getcwd() . '/' . $matches[1];
        $outFile = getcwd() . '/' . str_replace('.php', '.out', $matches[1]);
        $phpCode = File::load($phpFile);
        $output  = File::load($outFile);
        if (!$output) {
            $output = safe(shell_exec('php ' . $phpFile));
        }
        File::save($outFile, $output);
        return "```php\n$phpCode\n```\n\n"
            . "<details><summary>Output</summary>\n\n\n"
            . "```none\n$output\n```\n\n"
            . "</details>\n\n";
    }, $content);
    File::save($outFile, $content);
}

function safe(string $s): string
{
    return preg_replace('~\d+.\d+.\d+.\d+~', 'xx.xx.xx.xx', $s);
}