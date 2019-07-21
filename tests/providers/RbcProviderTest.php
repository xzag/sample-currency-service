<?php

namespace xzag\currency\tests\providers;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use xzag\currency\exceptions\ProviderException;
use xzag\currency\ExchangeRateRequest;
use xzag\currency\providers\RbcProvider;
use xzag\currency\tests\mock\ClientMock;

class RbcProviderTest extends TestCase
{
    /**
     * @var ClientMock
     */
    private $_mock;

    public function setUp(): void
    {
        parent::setUp();

        $this->_mock = new ClientMock();
    }

    public function testCorrectExchangeRate()
    {
        $this->_mock->addMockResponse(new Response(200, [], file_get_contents(dirname(__DIR__) . '/data/RbcResponseUSDRUB.json')));

        $provider = new RbcProvider($this->_mock->getClient());
        $this->assertEquals(62.8666, $provider->getExchangeRate(new ExchangeRateRequest('USD', 'RUB'))->getRate());
    }

    public function testCorrectExchangeRateWithNonDefaultBaseCurrency()
    {
        $this->_mock->addMockResponse(new Response(200, [], file_get_contents(dirname(__DIR__) . '/data/RbcResponseUSDEUR.json')));

        $provider = new RbcProvider($this->_mock->getClient());
        $this->assertEquals(0.888, $provider->getExchangeRate(new ExchangeRateRequest('USD', 'EUR'))->getRate());
    }

    public function testSameCurrencyExchange()
    {

        $provider = new RbcProvider($this->_mock->getClient());
        $this->assertEquals(1.0, $provider->getExchangeRate(new ExchangeRateRequest('USD', 'USD'))->getRate());
    }

    public function testNotFoundCurrency()
    {

        $provider = new RbcProvider($this->_mock->getClient());
        $this->_mock->addMockResponse(new Response(200, [], file_get_contents(dirname(__DIR__) . '/data/RbcResponseInvalid.json')));
        $this->expectException(ProviderException::class);
        $provider->getExchangeRate(new ExchangeRateRequest('BAD', 'USD'))->getRate();
    }

    public function testUnavailableResponse()
    {
        $this->_mock->addMockResponse(new Response(500, []));

        $provider = new RbcProvider($this->_mock->getClient());

        $this->expectException(ProviderException::class);
        $provider->getExchangeRate(new ExchangeRateRequest('USD', 'RUB'))->getRate();
    }

    public function testResponseWithDate()
    {
        $this->_mock->addMockResponse(new Response(200, [], file_get_contents(dirname(__DIR__) . '/data/RbcResponseUSDEUR.json')));

        $provider = new RbcProvider($this->_mock->getClient());
        $this->assertEquals(
            0.888,
            $provider->getExchangeRate(
                new ExchangeRateRequest('USD', 'EUR', new \DateTime('2019-07-20'))
            )->getRate()
        );
    }

    public function testCompatibleCurrencyRequest()
    {
        $this->_mock->addMockResponse(new Response(200, [], file_get_contents(dirname(__DIR__) . '/data/RbcResponseUSDRUB.json')));

        $provider = new RbcProvider($this->_mock->getClient());
        $this->assertEquals(62.8666, $provider->getExchangeRate(new ExchangeRateRequest('USD', 'RUR'))->getRate());
    }

    public function testMalformedResponse()
    {
        $this->_mock->addMockResponse(new Response(200, [], file_get_contents(dirname(__DIR__) . '/data/RbcResponseMalformed.json')));

        $provider = new RbcProvider($this->_mock->getClient());
        $this->expectException(ProviderException::class);
        $provider->getExchangeRate(new ExchangeRateRequest('USD', 'RUR'));
    }
}
