<?php

namespace xzag\currency\tests\mock;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ClientMock
 * @package xzag\currency\tests\mock
 */
class ClientMock
{
    /**
     * @var MockHandler
     */
    private $mock;

    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct()
    {
        $this->mock   = new MockHandler();
        $this->client = new Client([
            'handler' => HandlerStack::create($this->mock)
        ]);
    }

    /**
     * @return ClientInterface
     */
    public function getClient() : ClientInterface
    {
        return $this->client;
    }

    /**
     * @param ResponseInterface $response
     */
    public function addMockResponse(ResponseInterface $response)
    {
        $this->mock->append($response);
    }
}
