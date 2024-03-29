<?php

namespace xzag\currency\providers;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;
use xzag\currency\exceptions\ProviderException;
use xzag\currency\ExchangeRateProvider;
use xzag\currency\ExchangeRateRequest;

/**
 * Class CbrProvider
 * @package xzag\currency\providers
 */
class CbrProvider extends AbstractProvider
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
            case 'RUR':
                return ExchangeRateProvider::CURRENCY_RUB;
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
        $compRequest = parent::makeCompatibleRequest($rateRequest);
        $request = clone $compRequest;
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
        $params = [];
        if ($date = $rateRequest->getDate()) {
            $params['date_req'] = $date->format('d/m/Y');
        }

        return new Request(
            'GET',
            'http://www.cbr.ru/scripts/XML_daily.asp' . ($params ? '?' . http_build_query($params) : '')
        );
    }

    /**
     * @param ExchangeRateRequest $rateRequest
     * @param ResponseInterface $response
     * @return float
     *
     * @throws ProviderException
     */
    protected function parseResponse(ExchangeRateRequest $rateRequest, ResponseInterface $response): float
    {
        $xml = new SimpleXMLElement($response->getBody());
        $nodes = $xml->xpath(
            sprintf('./Valute[CharCode="%s"]', $rateRequest->getCurrency())
        );
        if (empty($nodes) || count($nodes) !== 1) {
            throw new ProviderException(
                $this,
                sprintf(
                    'Requested currency (%s) not found in response',
                    $rateRequest->getCurrency()
                )
            );
        }

        $node = array_pop($nodes);
        return (float) (
            str_replace(',', '.', (string)$node->Value) / $node->Nominal
        );
    }

    /**
     * @return bool
     */
    public function supportsBaseCurrencyInRequest(): bool
    {
        return false;
    }
}
