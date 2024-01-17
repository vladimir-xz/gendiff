<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $firstFile = __DIR__ . "/fixtures/FlatOne.json";
        $secondFile = __DIR__ . "/fixtures/FlatTwo.json";
        $result = file_get_contents(__DIR__ . "/fixtures/hi-ku-test.txt");

        $this->assertEquals($result, genDiff($firstFile, $secondFile));
    }
}
