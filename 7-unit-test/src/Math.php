<?php

namespace Mukhoiran\Test;

class Math
{
    public static function add($a, $b)
    {
        return $a + $b;
    }

    public static function subtract($a, $b)
    {
        return $a - $b;
    }

    public static function sum(array $numbers): int
    {
        return array_sum($numbers);
    }
}