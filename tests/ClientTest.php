<?php

namespace PkPayPlus\Tests;

use PHPUnit\Framework\TestCase;
use PkPayPlus\Client;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;

class ClientTest extends TestCase
{
    public function testLoadsConfigFromDotEnvAndAddsAuthorizationHeader()
    {
        $history = [];
        $mock = new MockHandler([new Response(200, [], json_encode(['ok' => true]))]);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        $http = new HttpClient([
            'handler' => $stack,
            'base_uri' => getenv('PKPAYPLUS_BASE_URL') ?: 'https://api.pkpayplus.com',
        ]);

        $client = new Client([], $http);

        $client->request('GET', '/ping');

        $this->assertCount(1, $history);
        $req = $history[0]['request'];

        $this->assertSame('Bearer ' . getenv('PKPAYPLUS_SECRET_KEY'), $req->getHeaderLine('Authorization'));
        $this->assertSame('application/json', $req->getHeaderLine('Accept'));
    }
}