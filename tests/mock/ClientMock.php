<?php

namespace xzag\currency\tests\mock;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;

class ClientMock
{
    /**
     * @var MockHandler
     */
    private $_mock;

    /**
     * @var ClientInterface
     */
    private $_client;

    public function __construct()
    {
        $this->_mock = new MockHandler();
        $this->_client = new Client([
            'handler' => HandlerStack::create($this->_mock)
        ]);
    }

    /**
     * @return ClientInterface
     */
    public function getClient() : ClientInterface
    {
        return $this->_client;
    }

    /**
     * @param ResponseInterface $response
     */
    public function addMockResponse(ResponseInterface $response)
    {
        $this->_mock->append($response);
    }
}
