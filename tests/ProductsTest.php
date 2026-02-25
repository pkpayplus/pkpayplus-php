<?php

namespace PkPayPlus\Tests;

use PHPUnit\Framework\TestCase;
use PkPayPlus\Client;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;

class ProductsTest extends TestCase
{
    protected function setUp(): void
    {
        putenv('PKPAYPLUS_BASE_URL=http://localhost:8000');
        putenv('PKPAYPLUS_SECRET_KEY=sk_test_env');
    }

    public function testCreateProductHitsExpectedPath()
    {
        $history = [];

        $mock = new MockHandler([
            new Response(201, [], json_encode(['id' => 'prod_1']))
        ]);

        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        $http = new HttpClient([
            'handler' => $stack,
            'base_uri' => getenv('PKPAYPLUS_BASE_URL')
        ]);

        $sdk = new Client([], $http);

        $sdk->products()->create('m_1', ['name' => 'Test']);

        $this->assertCount(1, $history);

        $req = $history[0]['request'];

        $this->assertSame('POST', $req->getMethod());
        $this->assertSame('/merchant-api/merchants/m_1/products', $req->getUri()->getPath());
        $this->assertSame('localhost', $req->getUri()->getHost());
        $this->assertSame('Bearer sk_test_env', $req->getHeaderLine('Authorization'));
    }
}