<?php

namespace xzag\currency\exceptions;

use Throwable;
use xzag\currency\ExchangeRateProvider;

/**
 * Class ProviderException
 * @package xzag\currency\exceptions
 */
class ProviderException extends Exception
{
    /**
     * ProviderException constructor.
     *
     * @param ExchangeRateProvider $provider
     * @param string               $message
     * @param int                  $code
     * @param Throwable|null       $previous
     */
    public function __construct(ExchangeRateProvider $provider, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf("[%s]: %s", get_class($provider), $message), $code, $previous);
    }
}
