<?php

namespace xzag\currency;

use DateTimeInterface;

/**
 * Class ExchangeRateRequest
 * @package xzag\currency
 */
class ExchangeRateRequest
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
     * @var DateTimeInterface|null
     */
    private $date;

    /**
     * ExchangeRateRequest constructor.
     *
     * @param string                  $currency
     * @param string                  $baseCurrency
     * @param DateTimeInterface|null $date
     */
    public function __construct(
        string $currency,
        string $baseCurrency = ExchangeRateProvider::CURRENCY_RUB,
        ?DateTimeInterface $date = null
    ) {
        $this->currency = mb_strtoupper(trim($currency));
        $this->baseCurrency = mb_strtoupper(trim($baseCurrency));
        $this->date = $date;
    }

    /**
     * @param DateTimeInterface $date
     */
    public function setDate(DateTimeInterface $date)
    {
        $this->date = $date;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    /**
     * @param string $baseCurrency
     */
    public function setBaseCurrency(string $baseCurrency): void
    {
        $this->baseCurrency = $baseCurrency;
    }
}
