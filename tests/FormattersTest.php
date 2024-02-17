<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Formatters\chooseFormateAndPrint;
use function Differ\Formatters\plain\showPlain;

class FormattersTest extends TestCase
{
    public function testUnknownReportFormatException(): void
    {
        $this->expectExceptionMessage("Unknown report format: \"html\"!");
        chooseFormateAndPrint('html', []);
    }

    public function testUnknownSymbolOfValue(): void
    {
        $this->expectExceptionMessage("Unknown status of value: \"*\"!");
        showPlain([['status' => '*', 'symbol' => '  ', 'value' => 0]]);
    }
}
