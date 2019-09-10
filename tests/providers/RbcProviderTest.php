<?php

namespace xzag\currency\tests\providers;

use DateTime;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use xzag\currency\exceptions\ProviderException;
use xzag\currency\ExchangeRateRequest;
use xzag\currency\providers\RbcProvider;
use xzag\currency\tests\mock\ClientMock;

/**
 * Class RbcProviderTest
 * @package xzag\currency\tests\providers
 */
class RbcProviderTest extends TestCase
{
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
    }

    /**
     * @throws ProviderException
     */
    public function testCorrectExchangeRate()
    {
        $this->mock->addMockResponse(
            new Response(
                200,
                [],
                file_get_contents(dirname(__DIR__) . '/data/RbcResponseUSDRUB.json')
            )
        );

        $provider = new RbcProvider($this->mock->getClient());
        $this->assertEquals(
            62.8666,
            $provider->getExchangeRate(
                new ExchangeRateRequest('USD', 'RUB')
            )->getRate()
        );
    }

    /**
     * @throws ProviderException
     */
    public function testCorrectExchangeRateWithNonDefaultBaseCurrency()
    {
        $this->mock->addMockResponse(
            new Response(
                200,
                [],
                file_get_contents(dirname(__DIR__) . '/data/RbcResponseUSDEUR.json')
            )
        );

        $provider = new RbcProvider($this->mock->getClient());
        $this->assertEquals(
            0.888,
            $provider->getExchangeRate(
                new ExchangeRateRequest('USD', 'EUR')
            )->getRate()
        );
    }

    /**
     * @throws ProviderException
     */
    public function testSameCurrencyExchange()
    {
        $provider = new RbcProvider($this->mock->getClient());
        $this->assertEquals(
            1.0,
            $provider->getExchangeRate(
                new ExchangeRateRequest('USD', 'USD')
            )->getRate()
        );
    }

    /**
     * @throws ProviderException
     */
    public function testNotFoundCurrency()
    {
        $provider = new RbcProvider($this->mock->getClient());
        $this->mock->addMockResponse(
            new Response(
                200,
                [],
                file_get_contents(dirname(__DIR__) . '/data/RbcResponseInvalid.json')
            )
        );
        $this->expectException(ProviderException::class);
        $provider->getExchangeRate(new ExchangeRateRequest('BAD', 'USD'))->getRate();
    }

    /**
     * @throws ProviderException
     */
    public function testUnavailableResponse()
    {
        $this->mock->addMockResponse(new Response(500, []));

        $provider = new RbcProvider($this->mock->getClient());

        $this->expectException(ProviderException::class);
        $provider->getExchangeRate(new ExchangeRateRequest('USD', 'RUB'))->getRate();
    }

    /**
     * @throws ProviderException
     */
    public function testResponseWithDate()
    {
        $this->mock->addMockResponse(
            new Response(
                200,
                [],
                file_get_contents(dirname(__DIR__) . '/data/RbcResponseUSDEUR.json')
            )
        );

        $provider = new RbcProvider($this->mock->getClient());
        $this->assertEquals(
            0.888,
            $provider->getExchangeRate(
                new ExchangeRateRequest('USD', 'EUR', new DateTime('2019-07-20'))
            )->getRate()
        );
    }

    /**
     * @throws ProviderException
     */
    public function testCompatibleCurrencyRequest()
    {
        $this->mock->addMockResponse(
            new Response(
                200,
                [],
                file_get_contents(dirname(__DIR__) . '/data/RbcResponseUSDRUB.json')
            )
        );

        $provider = new RbcProvider($this->mock->getClient());
        $this->assertEquals(
            62.8666,
            $provider->getExchangeRate(
                new ExchangeRateRequest('USD', 'RUR')
            )->getRate()
        );
    }

    /**
     * @throws ProviderException
     */
    public function testMalformedResponse()
    {
        $this->mock->addMockResponse(
            new Response(
                200,
                [],
                file_get_contents(dirname(__DIR__) . '/data/RbcResponseMalformed.json')
            )
        );

        $provider = new RbcProvider($this->mock->getClient());
        $this->expectException(ProviderException::class);
        $provider->getExchangeRate(new ExchangeRateRequest('USD', 'RUR'));
    }
}
