<?php

declare(strict_types=1);

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Differ\Differ\genDiff;

function getPathToFixtures($path)
{
    return __DIR__  . "/fixtures/" . $path;
}

final class DifferTest extends TestCase
{
    public static function additionProvider(): array
    {
        $nestedFirstFileJson = "NestedOne.json";
        $nestedSecondFileJson = "NestedTwo.json";
        $resultNested = "ExampleNested.txt";
        $nestedFirstFileYaml = "NestedOne.yaml";
        $nestedSecondFileYaml = "NestedTwo.yml";
        $resultPlain = "ResultPlain.txt";
        $resultJson = "ResultOfJson.json";

        return [
            'Nested Json'  => [$nestedFirstFileJson, $nestedSecondFileJson, 'stylish',$resultNested],
            'Nested Yaml' => [$nestedFirstFileYaml, $nestedSecondFileYaml, 'stylish', $resultNested],
            'Plain' => [$nestedFirstFileYaml, $nestedSecondFileYaml, 'plain', $resultPlain],
            'Json output'  => [$nestedFirstFileYaml, $nestedSecondFileYaml, 'json', $resultJson],
        ];
    }

    /**
     * @dataProvider additionProvider
     */
    public function testGenDiff(string $arrayOne, string $arrayTwo, string $format, string $expected): void
    {
        $pathToFiles = array_map(fn ($file) => getPathToFixtures($file), [$arrayOne, $arrayTwo, $expected]);
        $this->assertStringEqualsFile($pathToFiles[2], genDiff($pathToFiles[0], $pathToFiles[1], $format));
    }
}
