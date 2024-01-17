<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $firstFileJson = __DIR__ . "/fixtures/FlatOne.json";
        $secondFileJson = __DIR__ . "/fixtures/FlatTwo.json";
        $result = file_get_contents(__DIR__ . "/fixtures/example.txt");

        $this->assertEquals($result, genDiff($firstFileJson, $secondFileJson));

        $firstFileYml = __DIR__ . "/fixtures/FlatOne.yaml";
        $secondFileYml = __DIR__ . "/fixtures/FlatTwo.yaml";

        $this->assertEquals($result, genDiff($firstFileYml, $secondFileYml));
    }
}
