<?php

declare(strict_types=1);

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Differ\Differ\genDiff;

final class DifferTest extends TestCase
{
    public static function additionProvider(): array
    {
        $nestedFirstFileJson = __DIR__ . "/fixtures/NestedOne.json";
        $nestedSecondFileJson = __DIR__ . "/fixtures/NestedTwo.json";
        $resultNested = file_get_contents(__DIR__ . "/fixtures/ExampleNested.txt");
        $nestedFirstFileYaml = __DIR__ . "/fixtures/NestedOne.yaml";
        $nestedSecondFileYaml = __DIR__ . "/fixtures/NestedTwo.yml";
        $resultPlain = file_get_contents(__DIR__ . "/fixtures/ResultPlain.txt");
        $resultJson = __DIR__ . "/fixtures/ResultOfJson.json";
        $resultJsonContent = file_get_contents($resultJson, true);

        return [
            'Nested Json'  => [$nestedFirstFileJson, $nestedSecondFileJson, 'stylish', $resultNested],
            'Nested Yaml' => [$nestedFirstFileYaml, $nestedSecondFileYaml, 'stylish', $resultNested],
            'Plain' => [$nestedFirstFileYaml, $nestedSecondFileYaml, 'plain', $resultPlain],
            'Json output'  => [$nestedFirstFileYaml, $nestedSecondFileYaml, 'json', $resultJsonContent],
        ];
    }

    /**
     * @dataProvider additionProvider
     */
    public function testGenDiff(string $arrayOne, string $arrayTwo, string $format, string $expected): void
    {
        $this->assertEquals($expected, genDiff($arrayOne, $arrayTwo, $format));
    }
}
