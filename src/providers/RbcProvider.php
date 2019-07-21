<?php

namespace xzag\currency\providers;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use xzag\currency\exceptions\ProviderException;
use xzag\currency\ExchangeRateProvider;
use xzag\currency\ExchangeRateRequest;

class RbcProvider extends AbstractProvider
{
    /**
     * @return string
     */
    public function getBaseCurrency(): string
    {
        return static::CURRENCY_RUB;
    }

    /**
     * @param string $currency
     * @return string
     */
    public function swapCurrency(string $currency) : string
    {
        switch ($currency) {
            case ExchangeRateProvider::CURRENCY_RUB:
                return 'RUR';
            default:
                return $currency;
        }
    }

    /**
     * @param ExchangeRateRequest $rateRequest
     * @return ExchangeRateRequest
     */
    protected function makeCompatibleRequest(ExchangeRateRequest $rateRequest) : ExchangeRateRequest
    {
        $request = clone $rateRequest;
        $request->setCurrency($this->swapCurrency($rateRequest->getCurrency()));
        $request->setBaseCurrency($this->swapCurrency($rateRequest->getBaseCurrency()));
        return $request;
    }

    /**
     * @param ExchangeRateRequest $rateRequest
     * @return RequestInterface
     */
    protected function createHttpRequest(ExchangeRateRequest $rateRequest): RequestInterface
    {
        $params = [
            'currency_from' => $rateRequest->getCurrency(),
            'currency_to' => $rateRequest->getBaseCurrency(),
            'source' => 'cbrf',
            'sum' => 1,
        ];
        if ($date = $rateRequest->getDate()) {
            $params['date'] = $date->format('Y-m-d');
        }

        return new Request(
            'GET',
            'https://cash.rbc.ru/cash/json/converter_currency_rate/?'
                . http_build_query($params)
        );
    }

    /**
     * @param ExchangeRateRequest $rateRequest
     * @param ResponseInterface $response
     * @return float
     * @throws ProviderException
     */
    protected function parseResponse(ExchangeRateRequest $rateRequest, ResponseInterface $response): float
    {
        $json = json_decode($response->getBody(), true);
        $statusCode = $json['status'] ?? null;
        if ($statusCode !== 200) {
            throw new ProviderException(
                $this,
                sprintf('Invalid response status (%s)', $statusCode)
            );
        }

        $rate = $json['data']['rate1'] ?? null;
        if (!$rate) {
            throw new ProviderException($this, 'Invalid response');
        }

        return (float)$rate;
    }

    /**
     * @return bool
     */
    public function supportsBaseCurrencyInRequest(): bool
    {
        return true;
    }
}
