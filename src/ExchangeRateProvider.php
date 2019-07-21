<?php

namespace xzag\currency;

use xzag\currency\exceptions\ProviderException;

/**
 * Interface ExchangeRateProvider
 * @package xzag\currency
 */
interface ExchangeRateProvider
{
    public const CURRENCY_RUB = 'RUB';
    public const CURRENCY_USD = 'USD';
    public const CURRENCY_EUR = 'EUR';

    /**
     * @param ExchangeRateRequest $request
     * @return ExchangeRate
     * @throws ProviderException
     */
    public function getExchangeRate(ExchangeRateRequest $request) : ExchangeRate;

    /**
     * @return bool
     */
    public function supportsBaseCurrencyInRequest() : bool;
}
