<?php

namespace Mukhoiran\MVCProject\Tests;

use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    public function testRegex(): void
    {
        $path = "/products/12345/categories/abcde";
        $pattern = "#^/products/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)$#";

        $result = preg_match($pattern, $path, $variables);
        $this->assertEquals(1,$result);
        array_shift($variables);
        var_dump($variables);
    }
}