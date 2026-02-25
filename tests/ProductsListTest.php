<?php

namespace PkPayPlus\Tests;

use PHPUnit\Framework\TestCase;
use PkPayPlus\Client;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;

class ProductsListTest extends TestCase
{
    protected function setUp(): void
    {
        putenv('PKPAYPLUS_BASE_URL=http://localhost:8000');
        putenv('PKPAYPLUS_SECRET_KEY=sk_test_env');
    }

    public function testListProductsHitsExpectedPathAndQuery()
    {
        $history = [];

        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => 'prod_1', 'name' => 'A'],
                    ['id' => 'prod_2', 'name' => 'B'],
                ]
            ]))
        ]);

        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        $http = new HttpClient([
            'handler' => $stack,
            'base_uri' => getenv('PKPAYPLUS_BASE_URL'),
        ]);

        $sdk = new Client([], $http);

        $res = $sdk->products()->list([
            'per_page' => 20,
            'page' => 2,
            'search' => 'tee',
        ]);

        $this->assertCount(1, $history);

        $req = $history[0]['request'];

        $this->assertSame('GET', $req->getMethod());
        $this->assertSame('/merchant-api/products', $req->getUri()->getPath());
        $this->assertSame('per_page=20&page=2&search=tee', $req->getUri()->getQuery());
        $this->assertSame('Bearer sk_test_env', $req->getHeaderLine('Authorization'));

        $this->assertIsArray($res);
        $this->assertArrayHasKey('data', $res);
        $this->assertCount(2, $res['data']);
    }
}