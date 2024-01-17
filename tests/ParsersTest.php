<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Processing\getJsonContent;

class ParsersTest extends TestCase
{
    public function testException(): void
    {
        //$this->expectException("File do not found");
        $this->expectExceptionMessage("File do not found: \"/notexist.txt\"!");
        getJsonContent('/notexist.txt');
    }
}
