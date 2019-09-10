<?php

namespace xzag\currency\tests;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use xzag\currency\exceptions\ConfigurationException;
use xzag\currency\exceptions\InvalidValueException;
use xzag\currency\exceptions\ProviderException;
use xzag\currency\ExchangeRateRequest;
use xzag\currency\providers\CbrProvider;
use xzag\currency\providers\RbcProvider;
use xzag\currency\Service;
use xzag\currency\tests\mock\ClientMock;

/**
 * Class ServiceTest
 * @package xzag\currency\tests
 */
class ServiceTest extends TestCase
{
    /**
     * @var Service
     */
    private $service;

    /**
     * @var ClientMock
     */
    private $mock;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mock = new ClientMock();

        $this->service = new Service();
        $this->service->setProviders(
            [
                new CbrProvider($this->mock->getClient()),
                new RbcProvider($this->mock->getClient())
            ]
        );
    }

    /**
     * @throws ConfigurationException
     * @throws ProviderException
     * @throws InvalidValueException
     */
    public function testEmptyProvidersListException()
    {
        $this->service->setProviders([]);
        $this->expectException(ConfigurationException::class);
        $this->service->getAverageExchangeRate(
            new ExchangeRateRequest('USD', 'RUB')
        );
    }

    /**
     * @throws ConfigurationException
     * @throws ProviderException
     * @throws InvalidValueException
     */
    public function testCorrectExchangeRates()
    {
        $this->mock->addMockResponse(
            new Response(200, [], file_get_contents(__DIR__ . '/data/CbrResponseValid.xml'))
        );
        $this->mock->addMockResponse(
            new Response(200, [], file_get_contents(__DIR__ . '/data/RbcResponseUSDRUB.json'))
        );

        $this->assertEquals(62.8666, $this->service->getAverageExchangeRate(
            new ExchangeRateRequest('USD', 'RUB')
        )->getRate());
    }

    /**
     * @throws ConfigurationException
     * @throws ProviderException
     * @throws InvalidValueException
     */
    public function testCorrectExchangeRatesToNonBaseCurrency()
    {
        $this->mock->addMockResponse(
            new Response(200, [], file_get_contents(__DIR__ . '/data/CbrResponseValid.xml'))
        );
        $this->mock->addMockResponse(
            new Response(200, [], file_get_contents(__DIR__ . '/data/CbrResponseValid.xml'))
        );
        $this->mock->addMockResponse(
            new Response(200, [], file_get_contents(__DIR__ . '/data/RbcResponseUSDEUR.json'))
        );

        $this->assertEquals(
            0.888,
            $this->service->getAverageExchangeRate(
                new ExchangeRateRequest('USD', 'EUR')
            )->getRate()
        );
    }

    /**
     * @throws ConfigurationException
     * @throws ProviderException
     */
    public function testInvalidCurrency()
    {
        $this->mock->addMockResponse(
            new Response(200, [], file_get_contents(__DIR__ . '/data/CbrResponseValid.xml'))
        );
        $this->mock->addMockResponse(
            new Response(200, [], file_get_contents(__DIR__ . '/data/RbcResponseUSDRUB.json'))
        );

        $this->expectException(ProviderException::class);
        $this->service->getExchangeRates(new ExchangeRateRequest('WRONG', 'RUB'));
    }

    /**
     * @throws ConfigurationException
     * @throws InvalidValueException
     * @throws ProviderException
     */
    public function testExchangeToSameCurrency()
    {
        $this->assertEquals(1.0, $this->service->getAverageExchangeRate(
            new ExchangeRateRequest('USD', 'USD')
        )->getRate());
    }

    /**
     * @throws ConfigurationException
     * @throws InvalidValueException
     * @throws ProviderException
     */
    public function testExchangeToBaseCurrency()
    {
        $this->mock->addMockResponse(
            new Response(200, [], file_get_contents(__DIR__ . '/data/CbrResponseValid.xml'))
        );
        $this->mock->addMockResponse(
            new Response(200, [], file_get_contents(__DIR__ . '/data/RbcResponseRUBUSD.json'))
        );

        $this->assertEquals(0.0159, $this->service->getAverageExchangeRate(
            new ExchangeRateRequest('RUB', 'USD')
        )->getRate());
    }

    /**
     * @throws ConfigurationException
     * @throws InvalidValueException
     * @throws ProviderException
     */
    public function testUnavailableResponse()
    {
        $this->mock->addMockResponse(new Response(204, []));

        $this->expectException(ProviderException::class);
        $this->service->getAverageExchangeRate(
            new ExchangeRateRequest('USD', 'RUB')
        )->getRate();
    }
}
