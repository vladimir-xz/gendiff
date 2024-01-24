<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $nestedFirstFileJson = __DIR__ . "/fixtures/NestedOne.json";
        $nestedSecondFileJson = __DIR__ . "/fixtures/NestedTwo.json";
        $resultNested = file_get_contents(__DIR__ . "/fixtures/exampleNested.txt");

        $this->assertEquals($resultNested, genDiff($nestedFirstFileJson, $nestedSecondFileJson, 'stylish'));

        $nestedFirstFileYaml = __DIR__ . "/fixtures/NestedOne.yaml";
        $nestedSecondFileYaml = __DIR__ . "/fixtures/NestedTwo.yaml";

        $this->assertEquals($resultNested, genDiff($nestedFirstFileYaml, $nestedSecondFileYaml, 'stylish'));

        $resultPlain = file_get_contents(__DIR__ . "/fixtures/resultPlain.txt");

        $this->assertEquals($resultPlain, genDiff($nestedFirstFileYaml, $nestedSecondFileYaml, 'plain'));

        $resultJson = __DIR__ . "/fixtures/resultOfJson.json";
        $resultJsonContent = file_get_contents($resultJson, true);

        $this->assertEquals($resultJsonContent, genDiff($nestedFirstFileYaml, $nestedSecondFileYaml, 'json'));
    }
}
