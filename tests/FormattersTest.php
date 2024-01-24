<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Formatters\chooseFormate;
use function Differ\Formatters\plain\showPlain;

class FormattersTest extends TestCase
{
    public function testUnknownReportFormatException(): void
    {
        $this->expectExceptionMessage("Unknown report format: \"html\"!");
        chooseFormate('html', []);
    }

    public function testUnknownSymbolOfValue(): void
    {
        $this->expectExceptionMessage("Unknown symbol of value: \"*\"!");
        showPlain([['symbol' => '*', 'value' => 0]]);
    }
}
