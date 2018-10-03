<?php

namespace Algolia\AlgoliaSearch\Tests\Unit;

use Algolia\AlgoliaSearch\Client;
use Algolia\AlgoliaSearch\Http\HttpClientFactory;
use Algolia\AlgoliaSearch\Tests\RequestHttpClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

abstract class RequestTestCase extends TestCase
{
    /** @var \Algolia\AlgoliaSearch\Client */
    protected static $client;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        HttpClientFactory::set(function () {
            return new RequestHttpClient();
        });
        static::$client = Client::create('id', 'key');
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        HttpClientFactory::reset();
    }

    protected function assertEndpointEquals(RequestInterface $request, $endpoint)
    {
        $this->assertEquals($endpoint, $request->getUri()->getPath());
    }

    protected function assertBodySubset($subset, RequestInterface $request)
    {
        $body = json_decode((string) $request->getBody(), true);
        $this->assertArraySubset($subset, $body);
    }

    protected function assertQueryParametersSubset(array $subset, RequestInterface $request)
    {
        $params = $this->requestQueryParametersToArray($request);
        $this->assertArraySubset($subset, $params);
    }

    protected function assertQueryParametersNotHasKey($key, RequestInterface $request)
    {
        $params = $this->requestQueryParametersToArray($request);
        $this->assertArrayNotHasKey($key, $params);
    }

    private function requestQueryParametersToArray(RequestInterface $request)
    {
        $array = array();
        parse_str($request->getUri()->getQuery(), $array);

        return $array;
    }
}
