<?php

namespace xzag\currency\providers;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use xzag\currency\exceptions\ProviderException;
use xzag\currency\ExchangeRate;
use xzag\currency\ExchangeRateProvider;
use xzag\currency\ExchangeRateRequest;

abstract class AbstractProvider implements ExchangeRateProvider
{
    /**
     * @var ClientInterface|null
     */
    protected $client;

    /**
     * AbstractProvider constructor.
     * @param ClientInterface|null $client
     */
    public function __construct(?ClientInterface $client = null)
    {
        $this->client = $client;
    }

    /**
     * @return ClientInterface
     */
    public function getClient() : ClientInterface
    {
        if (!isset($this->client)) {
            $this->setClient(new Client());
        }

        return $this->client;
    }

    /**
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    abstract protected function getBaseCurrency() : string;

    /**
     * @param ExchangeRateRequest $rateRequest
     * @return RequestInterface
     */
    abstract protected function createHttpRequest(ExchangeRateRequest $rateRequest) : RequestInterface;

    /**
     * @param ExchangeRateRequest $rateRequest
     * @param ResponseInterface $response
     * @return float
     */
    abstract protected function parseResponse(ExchangeRateRequest $rateRequest, ResponseInterface $response) : float;

    /**
     * @param ExchangeRateRequest $rateRequest
     * @return ExchangeRateRequest
     */
    protected function makeCompatibleRequest(ExchangeRateRequest $rateRequest) : ExchangeRateRequest
    {
        return $rateRequest;
    }

    /**
     * @param ExchangeRateRequest $rateRequest
     * @return ExchangeRate
     * @throws ProviderException
     * @throws GuzzleException
     */
    public function getExchangeRate(ExchangeRateRequest $rateRequest): ExchangeRate
    {
        $request = $this->makeCompatibleRequest($rateRequest);

        if ($request->getCurrency() === $request->getBaseCurrency()) {
            $rate = 1.0;
        } elseif ($this->supportsBaseCurrencyInRequest() || $request->getBaseCurrency() === $this->getBaseCurrency()) {
            $rate = $this->getExchangeRateValue($request);
        } else {
            $fromRateRequest = clone $request;
            $fromRateRequest->setCurrency($request->getCurrency());

            $toRateRequest = clone $request;
            $toRateRequest->setCurrency($request->getBaseCurrency());

            $rate = round(
                $this->getExchangeRateValue($fromRateRequest) / $this->getExchangeRateValue($toRateRequest),
                '4'
            );
        }

        return new ExchangeRate(
            $rateRequest->getCurrency(),
            $rateRequest->getBaseCurrency(),
            $rate
        );
    }

    /**
     * @param ExchangeRateRequest $rateRequest
     * @return float
     * @throws ProviderException
     * @throws GuzzleException
     */
    protected function getExchangeRateValue(ExchangeRateRequest $rateRequest) : float
    {
        if ($rateRequest->getCurrency() === $this->getBaseCurrency()) {
            return 1.0;
        }

        try {
            $response = $this->getClient()->send($this->createHttpRequest($rateRequest));

            if ($response->getStatusCode() !== 200) {
                throw new ProviderException($this, sprintf("Invalid response status (%s)", $response->getStatusCode()));
            }

            return $this->parseResponse($rateRequest, $response);
        } catch (GuzzleException $e) {
            throw new ProviderException($this, $e->getMessage(), 0, $e);
        }
    }
}
