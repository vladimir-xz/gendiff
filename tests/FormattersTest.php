<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Formatters\chooseFormateAndPrint;
use function Differ\Formatters\Plain\format;

class FormattersTest extends TestCase
{
    public function testUnknownReportFormatException(): void
    {
        $this->expectExceptionMessage("Unknown report format: \"html\"!");
        chooseFormateAndPrint('html', []);
    }

    public function testUnknownStatusOfValue(): void
    {
        $this->expectExceptionMessage("Unknown status of value: \"*\"!");
        format([['status' => '*', 'symbol' => '  ', 'difference' => ['key' => 'value']]]);
    }
}
