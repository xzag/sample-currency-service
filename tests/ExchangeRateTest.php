<?php

namespace xzag\currency\tests;

use PHPUnit\Framework\TestCase;
use xzag\currency\ExchangeRate;

/**
 * Class ExchangeRateTest
 * @package xzag\currency\tests
 */
class ExchangeRateTest extends TestCase
{
    /**
     *
     */
    public function testRateProperty()
    {
        $exchangeRate = new ExchangeRate('RUB', 'USD', 1.0);
        $exchangeRate->setRate(45.0);
        $this->assertEquals(45.0, $exchangeRate->getRate());
    }

    /**
     *
     */
    public function testCurrencyProperty()
    {
        $exchangeRate = new ExchangeRate('RUB', 'USD', 1.0);
        $this->assertEquals('RUB', $exchangeRate->getCurrency());
        $this->assertEquals('USD', $exchangeRate->getBaseCurrency());
    }
}
