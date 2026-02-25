<?php

namespace PkPayPlus\Tests\Integration;

use PHPUnit\Framework\TestCase;
use PkPayPlus\Client;

class ProductsIntegrationTest extends TestCase
{
    private Client $sdk;

    protected function setUp(): void
    {
        if (getenv('PKPAYPLUS_INTEGRATION') !== '1') {
            $this->markTestSkipped('Set PKPAYPLUS_INTEGRATION=1 to run integration tests.');
        }

        if (!getenv('PKPAYPLUS_BASE_URL') || !getenv('PKPAYPLUS_SECRET_KEY')) {
            $this->markTestSkipped('Missing PKPAYPLUS_BASE_URL or PKPAYPLUS_SECRET_KEY.');
        }

        $this->sdk = new Client([]);
    }

    public function testListProducts()
    {
        $res = $this->sdk->products()->list(['per_page' => 5]);

        $this->assertIsArray($res);
        $this->assertArrayHasKey('data', $res);
        $this->assertIsArray($res['data']);

        fwrite(STDERR, "\n" . json_encode($res, JSON_PRETTY_PRINT) . "\n");
    }

    public function testCreateUpdateDeleteProduct()
    {
        $unique = 'SDK Test ' . date('YmdHis');

        $created = $this->sdk->products()->create([
            'name' => $unique,
            'currency' => 'USD',
            'unit_amount' => 100,
            'status' => 'draft',
        ]);

        $this->assertIsArray($created);
        $this->assertArrayHasKey('product', $created);

        $productId = $created['product']['id'] ?? null;
        $this->assertNotEmpty($productId);

        $updated = $this->sdk->products()->update($productId, [
            'name' => $unique . ' Updated'
        ]);

        $this->assertIsArray($updated);
        $this->assertSame($unique . ' Updated', $updated['product']['name'] ?? null);

        $deleted = $this->sdk->products()->delete($productId);

        $this->assertIsArray($deleted);

        fwrite(STDERR, "\nCREATE/UPDATE/DELETE OK for {$productId}\n");
    }
}