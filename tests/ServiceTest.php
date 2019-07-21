<?php

namespace xzag\currency\tests;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use xzag\currency\exceptions\ConfigurationException;
use xzag\currency\exceptions\ProviderException;
use xzag\currency\ExchangeRateRequest;
use xzag\currency\providers\CbrProvider;
use xzag\currency\providers\RbcProvider;
use xzag\currency\Service;
use xzag\currency\tests\mock\ClientMock;

class ServiceTest extends TestCase
{
    /**
     * @var Service
     */
    private $_service;

    /**
     * @var ClientMock
     */
    private $_mock;

    public function setUp(): void
    {
        parent::setUp();

        $this->_mock = new ClientMock();

        $this->_service = new Service();
        $this->_service->setProviders([
            new CbrProvider($this->_mock->getClient()),
            new RbcProvider($this->_mock->getClient())
        ]);
    }

    public function testEmptyProvidersListException()
    {
        $this->_service->setProviders([]);
        $this->expectException(ConfigurationException::class);
        $this->_service->getAverageExchangeRate(new ExchangeRateRequest('USD', 'RUB'));
    }

    public function testCorrectExchangeRates()
    {
        $this->_mock->addMockResponse(new Response(200, [], file_get_contents(__DIR__ . '/data/CbrResponseValid.xml')));
        $this->_mock->addMockResponse(new Response(200, [], file_get_contents(__DIR__ . '/data/RbcResponseUSDRUB.json')));

        $this->assertEquals(62.8666, $this->_service->getAverageExchangeRate(
            new ExchangeRateRequest('USD', 'RUB'))->getRate()
        );
    }

    public function testCorrectExchangeRatesToNonBaseCurrency()
    {
        $this->_mock->addMockResponse(new Response(200, [], file_get_contents(__DIR__ . '/data/CbrResponseValid.xml')));
        $this->_mock->addMockResponse(new Response(200, [], file_get_contents(__DIR__ . '/data/CbrResponseValid.xml')));
        $this->_mock->addMockResponse(new Response(200, [], file_get_contents(__DIR__ . '/data/RbcResponseUSDEUR.json')));

        $this->assertEquals(0.888, $this->_service->getAverageExchangeRate(
            new ExchangeRateRequest('USD', 'EUR'))->getRate()
        );
    }


    public function testInvalidCurrency()
    {
        $this->_mock->addMockResponse(new Response(200, [], file_get_contents(__DIR__ . '/data/CbrResponseValid.xml')));
        $this->_mock->addMockResponse(new Response(200, [], file_get_contents(__DIR__ . '/data/RbcResponseUSDRUB.json')));

        $this->expectException(ProviderException::class);
        $this->_service->getExchangeRates(new ExchangeRateRequest('WRONG', 'RUB'));
    }

    public function testExchangeToSameCurrency()
    {
        $this->assertEquals(1.0, $this->_service->getAverageExchangeRate(
                new ExchangeRateRequest('USD', 'USD')
            )->getRate()
        );
    }

    public function testExchangeToBaseCurrency()
    {
        $this->_mock->addMockResponse(new Response(200, [], file_get_contents(__DIR__ . '/data/CbrResponseValid.xml')));
        $this->_mock->addMockResponse(new Response(200, [], file_get_contents(__DIR__ . '/data/RbcResponseRUBUSD.json')));

        $this->assertEquals(0.0159, $this->_service->getAverageExchangeRate(
            new ExchangeRateRequest('RUB', 'USD')
        )->getRate()
        );
    }

    public function testUnavailableResponse()
    {
        $this->_mock->addMockResponse(new Response(204, []));

        $this->expectException(ProviderException::class);
        $this->_service->getAverageExchangeRate(new ExchangeRateRequest('USD', 'RUB'))->getRate();
    }
}
