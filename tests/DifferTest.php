<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use Differ\Differ;

class UserTest extends TestCase
{
    public function testGenDiff(): void
    {
        // $name = 'john';
        // $children = [new User('Mark')];
        // $user = new User($name, $children);

        $this->assertEquals($name, $user->getName());
        $this->assertEquals(collect($children), $user->getChildren());
    }
}
