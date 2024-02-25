<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\chooseFormateAndPrint;
use function Differ\Differ\showPlain;

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
        showPlain([['status' => '*', 'symbol' => '  ', 'difference' => ['key' => 'value']]]);
    }
}
