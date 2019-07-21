## Currency Service 
[![Build Status](https://travis-ci.org/xzag/sample-currency-service.svg?branch=master)](https://travis-ci.org/xzag/sample-currency-service)

Get exchange rate from multiple providers

### Installation

`composer require xzag/sample-currency-service`

### Usage

```$service = new \xzag\currency\Service();
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

echo $rate; // 63.5841

```
