<?php

namespace xzag\currency\tests;

use PHPUnit\Framework\TestCase;
use xzag\currency\ExchangeRateRequest;

class ExchangeRateRequestTest extends TestCase
{
    public function testDateProperty()
    {
        $request = new ExchangeRateRequest('RUB', 'USD');
        $date = new \DateTime();
        $request->setDate($date);
        $this->assertEquals($date, $request->getDate());
    }

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
