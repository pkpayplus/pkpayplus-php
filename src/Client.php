<?php

namespace PkPayPlus;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;

class Client
{
    private HttpClient $http;
    private string $secretKey;

    public function __construct(array $config = [], ?HttpClient $http = null)
    {
        $baseUrl = rtrim(
            (string) (
                $config['base_url']
                ?? getenv('PKPAYPLUS_BASE_URL')
                ?? ($_ENV['PKPAYPLUS_BASE_URL'] ?? null)
                ?? 'https://api.pkpayplus.com'
            ),
            '/'
        );

        $this->secretKey = (string) (
            $config['secret_key']
            ?? getenv('PKPAYPLUS_SECRET_KEY')
            ?? ($_ENV['PKPAYPLUS_SECRET_KEY'] ?? null)
            ?? ''
        );

        if ($this->secretKey === '') {
            throw new \InvalidArgumentException('PkPayPlus secret_key is required.');
        }

        $this->http = $http ?: new HttpClient([
            'base_uri' => $baseUrl,
            'timeout' => (float) ($config['timeout'] ?? 30),
        ]);
    }

    public function products(): Resources\Products
    {
        return new Resources\Products($this);
    }

    public function sessions(): Resources\Sessions
    {
        return new Resources\Sessions($this);
    }

    public function request(string $method, string $path, array $options = []): array
    {
        $headers = $options['headers'] ?? [];
        $headers['Authorization'] = 'Bearer ' . $this->secretKey;
        $headers['Accept'] = 'application/json';
        $options['headers'] = $headers;

        try {
            $res = $this->http->request($method, ltrim($path, '/'), $options);
            $body = (string) $res->getBody();
            return $body === '' ? [] : (array) json_decode($body, true);
        } catch (RequestException $e) {
            $status = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $body = $e->getResponse() ? (string) $e->getResponse()->getBody() : '';
            $json = $body !== '' ? json_decode($body, true) : null;

            throw new \RuntimeException(json_encode([
                'status' => $status,
                'message' => $e->getMessage(),
                'response' => $json ?? $body,
            ]), $status ?: 500);
        }
    }
}