## Currency Service 
[![Build Status](https://travis-ci.org/xzag/sample-currency-service.svg?branch=master)](https://travis-ci.org/xzag/sample-currency-service)
[![Coverage Status](https://coveralls.io/repos/github/xzag/sample-currency-service/badge.svg?branch=master)](https://coveralls.io/github/xzag/sample-currency-service?branch=master)

Get exchange rate from multiple providers

### Installation

`composer require xzag/currency-service`

### Usage

```
$service = new \xzag\currency\Service();
$service->setProviders([
    new \xzag\currency\providers\CbrProvider(),
    new \xzag\currency\providers\RbcProvider()
]);

$rate = $service->getAverageExchangeRate(
    new \xzag\currency\ExchangeRateRequest(
        'USD',
        'RUB',
        new \DateTime('2019-07-08')
    )
); 

echo $rate->getRate(); // 63.5841

```
