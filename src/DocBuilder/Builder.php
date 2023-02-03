<?php

namespace Tivins\DocBuilder;

use Tivins\Core\Code\Exception;
use Tivins\Core\System\File;
use Tivins\Core\Tpl;

class Builder
{
    private string $source = '';
    private string $output = '';
    public function __construct(private readonly string $sourceFile, private readonly string $destinationFile)
    {
        $tpl = new Tpl(File::load($this->sourceFile));
        $tpl->addFunction('classdoc', function($source, &$encode) {
            try {
                $class = new \ReflectionClass($source);
                $doc = DocParser::parse($class->getDocComment());
                $encode = false;
                return $doc['brief'] ?? '';
            }
            catch (Exception $ex) {
                return $ex->getMessage();
            }
        });
        File::save($this->destinationFile, $tpl);
    }

}