<?php

use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NodeConnectingVisitor;
use PhpParser\ParserFactory;
use Tivins\Core\System\File;
use Tivins\Core\System\FileSys;

require '../vendor/autoload.php';

function genDoc(string $directory): void
{
    $iter = FileSys::getIterator($directory);
    foreach ($iter as $file) {
        if ($file->isDir()) {
            continue;
        }
        genDocFile($file->getRealPath());
    }
}

function genDocFile($getRealPath)
{
    $code = File::load($getRealPath);

    $lexer = new \PhpParser\Lexer(array(
        'usedAttributes' => array('comments', 'startLine', 'endLine', 'startFilePos', 'endFilePos'),
    ));
    $traverser = new NodeTraverser;
    $traverser->addVisitor(new NodeConnectingVisitor);
    $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7, $lexer);
    $stmts  = $traverser->traverse($parser->parse($code));

    parseStmts($stmts);

}

function parseStmts(array $stmts): void
{
    foreach ($stmts as $k => $node) {
        if ($node instanceof Class_) {
            parseClass($node);
        }
    }
}

function parseClass(Class_ $node)
{
    $methods = $node->getMethods();
}

genDoc('../src/lib/');