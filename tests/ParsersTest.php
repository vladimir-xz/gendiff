<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use Differ\FileProcessing;

use function Differ\Parsers\parseFile;

class ParsersTest extends TestCase
{
    public function testDoNotExistException(): void
    {
        $this->expectExceptionMessage("File do not found: \"/Notexist.txt\"!");
        FileProcessing\getFilesContent('/Notexist.txt');
    }
    public function testBadExtention(): void
    {
        $this->expectExceptionMessage("Unknow file extention: \"txt\"!");
        parseFile('txt', '');
    }
    public function testEmptyDile(): void
    {
        $empty = __DIR__ . "/fixtures/Empty.json";
        $this->expectExceptionMessage("File \"Empty.json\" is empty.");
        FileProcessing\getFilesContent($empty);
    }
}
