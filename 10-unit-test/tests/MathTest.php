<?php

namespace Mukhoiran\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MathTest extends TestCase
{
    //without dataProvider
    public function testAddition()
    {
        $this->assertEquals(4, Math::add(2, 2));
        $this->assertEquals(0, Math::add(-1, 1));
        $this->assertEquals(-3, Math::add(-1, -2));
    }

    //without dataProvider
    public function testSubtraction()
    {
        $this->assertEquals(0, Math::subtract(2, 2));
        $this->assertEquals(-2, Math::subtract(-1, 1));
        $this->assertEquals(1, Math::subtract(-1, -2));
    }

    //============ with DataProvider
    #[DataProvider('mathSumData')]
    public function testDataProvider(array $values, int $expected)
    {
        self::assertEquals($expected, Math::sum($values));
    }

    public static function mathSumData(): array
    {
        return [
            [[1, 2, 3], 6],
            [[-1, 1], 0],
            [[5, 5, 5, 5], 20],
            [[0, 0, 0], 0],
        ];
    }
    //============ End

    //=========== with TestWith
    #[TestWith([[1, 2, 3], 6])]
    #[TestWith([[-1, 1], 0])]
    #[TestWith([[5, 5, 5, 5], 20])]
    #[TestWith([[0, 0, 0], 0])]
    public function testWith(array $values, int $expected): void
    {
        self::assertEquals($expected, Math::sum($values));
    }
}