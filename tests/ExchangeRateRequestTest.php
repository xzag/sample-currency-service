<?php

namespace xzag\currency\tests;

use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use xzag\currency\ExchangeRateRequest;

/**
 * Class ExchangeRateRequestTest
 * @package xzag\currency\tests
 */
class ExchangeRateRequestTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testDateProperty()
    {
        $request = new ExchangeRateRequest('RUB', 'USD');
        $date = new DateTime();
        $request->setDate($date);
        $this->assertEquals($date, $request->getDate());
    }

    /**
     *
     */
    public function testCurrencyProperty()
    {
        $request = new ExchangeRateRequest('RUB', 'USD');
        $this->assertEquals('RUB', $request->getCurrency());
        $this->assertEquals('USD', $request->getBaseCurrency());

        $request->setCurrency('EUR');
        $request->setBaseCurrency('AUD');
        $this->assertEquals('EUR', $request->getCurrency());
        $this->assertEquals('AUD', $request->getBaseCurrency());
    }
}
