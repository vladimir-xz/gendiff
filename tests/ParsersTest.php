<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Parsers\parseFile;

class ParsersTest extends TestCase
{
    public function testDoNotExistException(): void
    {
        $this->expectExceptionMessage("File do not found: \"/notexist.txt\"!");
        parseFile('/notexist.txt');
    }
    public function testBadExtention(): void
    {
        $txtFile = __DIR__ . "/fixtures/example.txt";
        $this->expectExceptionMessage("Unknow file extention: \"txt\"!");
        parseFile($txtFile);
    }
    public function testEmptyDile(): void
    {
        $empty = __DIR__ . "/fixtures/empty.json";
        $this->expectExceptionMessage("File \"empty.json\" is empty.");
        parseFile($empty);
    }
}
