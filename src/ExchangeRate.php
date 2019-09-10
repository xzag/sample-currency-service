<?php

namespace xzag\currency;

/**
 * Class ExchangeRate
 * @package xzag\currency
 */
class ExchangeRate
{
    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $baseCurrency;

    /**
     * @var float
     */
    private $rate;
    /**
     * ExchangeRate constructor.
     * @param string $currency
     * @param string $baseCurrency
     * @param float $rate
     */
    public function __construct(string $currency, string $baseCurrency, float $rate)
    {
        $this->currency = $currency;
        $this->baseCurrency = $baseCurrency;
        $this->rate = $rate;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    /**
     * @param float $rate
     */
    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }
}
