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
        $resultFlat = file_get_contents(__DIR__ . "/fixtures/example.txt");

        $this->assertEquals($resultFlat, genDiff($firstFileJson, $secondFileJson));

        $firstFileYml = __DIR__ . "/fixtures/FlatOne.yaml";
        $secondFileYml = __DIR__ . "/fixtures/FlatTwo.yaml";

        $this->assertEquals($resultFlat, genDiff($firstFileYml, $secondFileYml));

        $nestedFirstFileJson = __DIR__ . "/fixtures/NestedOne.json";
        $nestedSecondFileJson = __DIR__ . "/fixtures/NestedTwo.json";
        $resultNested = file_get_contents(__DIR__ . "/fixtures/exampleNested.txt");

        $this->assertEquals($resultNested, genDiff($nestedFirstFileJson, $nestedSecondFileJson));

        $nestedFirstFileYaml = __DIR__ . "/fixtures/NestedOne.yaml";
        $nestedSecondFileYaml = __DIR__ . "/fixtures/NestedTwo.yaml";

        $this->assertEquals($resultNested, genDiff($nestedFirstFileYaml, $nestedSecondFileYaml));
    }
}
