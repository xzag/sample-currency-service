<?php

namespace xzag\currency;

use xzag\currency\exceptions\ConfigurationException;
use xzag\currency\exceptions\InvalidValueException;
use xzag\currency\exceptions\ProviderException;

/**
 * Class Service
 * @package xzag\currency
 */
class Service
{
    private $providers;

    /**
     * @param ExchangeRateProvider $provider
     */
    public function push(ExchangeRateProvider $provider): void
    {
        $this->providers[] = $provider;
    }

    /**
     * @param ExchangeRateProvider[] $providers
     */
    public function setProviders(array $providers): void
    {
        $this->providers = [];
        array_map(
            function (ExchangeRateProvider $provider) {
                $this->push($provider);
            },
            $providers
        );
    }

    /**
     * @param ExchangeRateRequest $request
     * @return ExchangeRatesCollection
     *
     * @throws ConfigurationException
     * @throws ProviderException
     */
    public function getExchangeRates(ExchangeRateRequest $request): ExchangeRatesCollection
    {
        if (empty($this->providers)) {
            throw new ConfigurationException('Empty providers list');
        }

        $rates = array_map(
            function (ExchangeRateProvider $provider) use ($request) {
                return $provider->getExchangeRate($request);
            },
            $this->providers
        );

        return new ExchangeRatesCollection($rates);
    }

    /**
     * @param ExchangeRateRequest $request
     * @return ExchangeRate
     *
     * @throws ConfigurationException
     * @throws InvalidValueException
     * @throws ProviderException
     */
    public function getAverageExchangeRate(ExchangeRateRequest $request): ExchangeRate
    {
        $collection = $this->getExchangeRates($request);
        return new ExchangeRate(
            $request->getCurrency(),
            $request->getBaseCurrency(),
            $collection->average()
        );
    }
}
