<?php

namespace xzag\currency;

use xzag\currency\exceptions\InvalidValueException;

/**
 * Class ExchangeRatesCollection
 * @package xzag\currency
 */
class ExchangeRatesCollection implements \IteratorAggregate
{
    /**
     * @var ExchangeRate[]
     */
    private $items;

    public function __construct(array $exchangeRates)
    {
        $this->items = $exchangeRates;
    }

    /**
     * @return bool
     */
    public function isEmpty() : bool
    {
        return empty($this->items);
    }

    /**
     * @return int
     */
    public function count() : int
    {
        return count($this->items);
    }

    /**
     * @return float
     * @throws InvalidValueException
     */
    public function average()
    {
        if ($this->isEmpty()) {
            throw new InvalidValueException('Empty rates collection');
        }

        if (!$this->isCommonCurrencies()) {
            throw new InvalidValueException('Cannot aggregate for different base currencies');
        }

        return array_reduce($this->items, function (float $acc, ExchangeRate $rate) {
            return $acc + $rate->getRate();
        }, 0.0) / $this->count();
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @param string|int $index
     * @return ExchangeRate
     */
    public function item($index) : ExchangeRate
    {
        return $this->getIterator()->offsetGet($index);
    }

    /**
     * @return bool
     */
    public function isCommonCurrencies() : bool
    {
        $currency = null;
        $baseCurrency = null;
        foreach ($this->items as $item) {
            if (!isset($currency)) {
                $currency = $item->getCurrency();
            }

            if (!isset($baseCurrency)) {
                $baseCurrency = $item->getBaseCurrency();
            }

            if ($currency !== $item->getCurrency() || $baseCurrency !== $item->getBaseCurrency()) {
                return false;
            }
        }

        return true;
    }
}
