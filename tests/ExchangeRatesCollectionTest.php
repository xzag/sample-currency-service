<?php

namespace xzag\currency\tests;

use PHPUnit\Framework\TestCase;
use xzag\currency\exceptions\InvalidValueException;
use xzag\currency\ExchangeRate;
use xzag\currency\ExchangeRatesCollection;

/**
 * Class ExchangeRatesCollectionTest
 * @package xzag\currency\tests
 */
class ExchangeRatesCollectionTest extends TestCase
{
    /**
     *
     */
    public function testEmptyCollection()
    {
        $collection = new ExchangeRatesCollection([]);
        $this->assertEquals(true, $collection->isEmpty());
        $this->assertEquals(0, $collection->count());
    }

    /**
     *
     */
    public function testNonEmptyCollection()
    {
        $collection = new ExchangeRatesCollection([
            new ExchangeRate('USD', 'RUB', 3.0),
        ]);
        $this->assertEquals(false, $collection->isEmpty());
        $this->assertEquals(1, $collection->count());
    }

    /**
     * @throws InvalidValueException
     */
    public function testAverage()
    {
        $collection = new ExchangeRatesCollection([
            new ExchangeRate('USD', 'RUB', 3.0),
            new ExchangeRate('USD', 'RUB', 6.0),
        ]);
        $this->assertEquals(4.5, $collection->average());
    }

    /**
     * @throws InvalidValueException
     */
    public function testAverageWithEmptyData()
    {
        $collection = new ExchangeRatesCollection([]);
        $this->expectException(InvalidValueException::class);
        $collection->average();
    }

    /**
     * @throws InvalidValueException
     */
    public function testAverageWithInvalidData()
    {
        $collection = new ExchangeRatesCollection([
            new ExchangeRate('USD', 'RUB', 3.0),
            new ExchangeRate('EUR', 'RUB', 6.0),
        ]);
        $this->expectException(InvalidValueException::class);
        $collection->average();
    }

    /**
     *
     */
    public function testIsCommonCurrencies()
    {
        $collection = new ExchangeRatesCollection([
            new ExchangeRate('USD', 'RUB', 3.0),
            new ExchangeRate('EUR', 'RUB', 6.0),
        ]);
        $this->assertEquals(false, $collection->isCommonCurrencies());

        $collection = new ExchangeRatesCollection([
            new ExchangeRate('USD', 'RUB', 3.0),
            new ExchangeRate('USD', 'RUB', 6.0),
        ]);
        $this->assertEquals(true, $collection->isCommonCurrencies());

        $collection = new ExchangeRatesCollection([
            new ExchangeRate('USD', 'RUB', 3.0),
            new ExchangeRate('USD', 'EUR', 6.0),
        ]);
        $this->assertEquals(false, $collection->isCommonCurrencies());
    }

    /**
     *
     */
    public function testIterator()
    {
        $collection = new ExchangeRatesCollection([
            new ExchangeRate('USD', 'RUB', 3.0),
            new ExchangeRate('EUR', 'RUB', 6.0),
        ]);

        foreach ($collection as $index => $item) {
            $this->assertEquals($item, $collection->item($index));
        }
    }
}
