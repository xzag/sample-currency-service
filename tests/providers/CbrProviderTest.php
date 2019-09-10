<?php

namespace xzag\currency\tests\providers;

use DateTime;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use xzag\currency\exceptions\ProviderException;
use xzag\currency\ExchangeRateRequest;
use xzag\currency\providers\CbrProvider;
use xzag\currency\tests\mock\ClientMock;

/**
 * Class CbrProviderTest
 * @package xzag\currency\tests\providers
 */
class CbrProviderTest extends TestCase
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
     *
     */
    public function testSetClient()
    {
        $client = $this->mock->getClient();
        $provider = new CbrProvider();
        $provider->setClient($client);
        $this->assertEquals($client, $provider->getClient());
    }

    /**
     *
     */
    public function testDefaultClient()
    {
        $provider = new CbrProvider();
        $this->assertInstanceOf(ClientInterface::class, $provider->getClient());
    }

    /**
     * @throws ProviderException
     */
    public function testCorrectExchangeRate()
    {
        $this->mock->addMockResponse(
            new Response(200, [], file_get_contents(dirname(__DIR__) . '/data/CbrResponseValid.xml'))
        );

        $provider = new CbrProvider($this->mock->getClient());
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
            new Response(200, [], file_get_contents(dirname(__DIR__) . '/data/CbrResponseValid.xml'))
        );
        $this->mock->addMockResponse(
            new Response(200, [], file_get_contents(dirname(__DIR__) . '/data/CbrResponseValid.xml'))
        );

        $provider = new CbrProvider($this->mock->getClient());
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

        $provider = new CbrProvider($this->mock->getClient());
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

        $provider = new CbrProvider($this->mock->getClient());
        $this->mock->addMockResponse(
            new Response(200, [], file_get_contents(dirname(__DIR__) . '/data/CbrResponseValid.xml'))
        );
        $this->expectException(ProviderException::class);
        $provider->getExchangeRate(new ExchangeRateRequest('WRONG', 'USD'))->getRate();
    }

    /**
     * @throws ProviderException
     */
    public function testUnavailableResponse()
    {

        $provider = new CbrProvider($this->mock->getClient());
        $this->mock->addMockResponse(new Response(500, []));
        $this->expectException(ProviderException::class);
        $provider->getExchangeRate(new ExchangeRateRequest('USD', 'RUB'))->getRate();
    }

    /**
     * @throws ProviderException
     */
    public function testResponseWithDate()
    {
        $this->mock->addMockResponse(
            new Response(200, [], file_get_contents(dirname(__DIR__) . '/data/CbrResponseValid.xml'))
        );
        $this->mock->addMockResponse(
            new Response(200, [], file_get_contents(dirname(__DIR__) . '/data/CbrResponseValid.xml'))
        );

        $provider = new CbrProvider($this->mock->getClient());
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
            new Response(200, [], file_get_contents(dirname(__DIR__) . '/data/CbrResponseValid.xml'))
        );

        $provider = new CbrProvider($this->mock->getClient());
        $this->assertEquals(
            62.8666,
            $provider->getExchangeRate(
                new ExchangeRateRequest('USD', 'RUR')
            )->getRate()
        );
    }
}
