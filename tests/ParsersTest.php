<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Parsers\parseFile;
use function Differ\Parsers\getFilesContent;

class ParsersTest extends TestCase
{
    public function testDoNotExistException(): void
    {
        $this->expectExceptionMessage("File do not found: \"/Notexist.txt\"!");
        getFilesContent('/Notexist.txt');
    }
    public function testBadExtention(): void
    {
        $txtFile = __DIR__ . "/fixtures/Example.txt";
        $this->expectExceptionMessage("Unknow file extention: \"txt\"!");
        parseFile($txtFile);
    }
    public function testEmptyDile(): void
    {
        $empty = __DIR__ . "/fixtures/Empty.json";
        $this->expectExceptionMessage("File \"Empty.json\" is empty.");
        getFilesContent($empty);
    }
}
