<?php

declare(strict_types=1);

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Differ\Differ\genDiff;
use function Differ\FilesProcessing\findDirectory;

const FIXTURES_DIR = __DIR__ . '/fixtures/';
// define("FIXTURES_DIR", findDirectory('fixtures'));

final class DifferTest extends TestCase
{
    public static function additionProvider(): array
    {
        $nestedFirstFileJson = "/NestedOne.json";
        $nestedSecondFileJson = "/NestedTwo.json";
        $resultNested = "/ExampleNested.txt";
        $nestedFirstFileYaml = "/NestedOne.yaml";
        $nestedSecondFileYaml = "/NestedTwo.yml";
        $resultPlain = "/ResultPlain.txt";
        $resultJson = "/ResultOfJson.json";

        return [
            'Nested Json'  => [FIXTURES_DIR . $nestedFirstFileJson, FIXTURES_DIR . $nestedSecondFileJson, 'stylish', FIXTURES_DIR . $resultNested],
            'Nested Yaml' => [FIXTURES_DIR . $nestedFirstFileYaml, FIXTURES_DIR . $nestedSecondFileYaml, 'stylish', FIXTURES_DIR . $resultNested],
            'Plain' => [FIXTURES_DIR . $nestedFirstFileYaml, FIXTURES_DIR . $nestedSecondFileYaml, 'plain', FIXTURES_DIR . $resultPlain],
            'Json output'  => [FIXTURES_DIR . $nestedFirstFileYaml, FIXTURES_DIR . $nestedSecondFileYaml, 'json', FIXTURES_DIR . $resultJson],
        ];
    }

    /**
     * @dataProvider additionProvider
     */
    public function testGenDiff(string $arrayOne, string $arrayTwo, string $format, string $expected): void
    {
        $this->assertStringEqualsFile($expected, genDiff($arrayOne, $arrayTwo, $format));
    }
}
