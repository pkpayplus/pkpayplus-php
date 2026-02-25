# PkPayPlus PHP SDK

Official PHP SDK for **PkPayPlus Merchant API**.

Use this SDK in **plain PHP** or **Laravel** to:

- Manage **Products** (CRUD)
- Create & verify **Checkout Sessions**
- Authenticate securely using your **Merchant Secret Key**

> ⚠️ This SDK is for **server-side (merchant backend)** usage only.  
> Never expose your secret key in frontend code.

---

# Requirements

- PHP 8.1+
- Composer

---

# Installation

```bash
composer require pkpayplus/pkpayplus-php
```

---

# Configuration

The SDK reads configuration in this order:

1. Config array passed to `Client`
2. Environment variables
3. Default base URL

---

## Environment Variables (Recommended)

Create a `.env` file:

```env
PKPAYPLUS_SECRET_KEY=sk_live_xxxxxxxxx
```

Never commit `.env` to version control.

---

# Quick Start (Plain PHP)

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use PkPayPlus\Client;

$pk = new Client();

$products = $pk->products()->list(['per_page' => 20]);

print_r($products);
```

---

## Passing Config Manually

```php
$pk = new Client([
  'base_url' => 'https://api.pkpayplus.com',
  'secret_key' => 'sk_live_xxx'
]);
```

---

# Authentication

All merchant API requests use:

```
Authorization: Bearer {secret_key}
```

The SDK sets this automatically.

---

# Products API

Merchant inferred from secret key.

Endpoints used:

```
GET    /merchant-api/products
POST   /merchant-api/products
GET    /merchant-api/products/{product_id}
PUT    /merchant-api/products/{product_id}
DELETE /merchant-api/products/{product_id}
```

---

## List Products

```php
$products = $pk->products()->list([
  'per_page' => 20,
  'page' => 1,
  'search' => 'shirt'
]);
```

---

## Create Product

```php
$created = $pk->products()->create([
  'name' => 'Black T-Shirt',
  'description' => 'Cotton tee',
  'status' => 'draft',

  'currency' => 'USD',
  'unit_amount' => 1999
]);

$productId = $created['product']['id'];
```

---

## Get Product

```php
$product = $pk->products()->get($productId);
```

---

## Update Product

```php
$updated = $pk->products()->update($productId, [
  'name' => 'Black T-Shirt (Updated)',
  'status' => 'published'
]);
```

---

## Delete Product

```php
$pk->products()->delete($productId);
```

---

# Checkout Sessions (Merchant API)

Used for backend-driven checkout flows (like WooCommerce plugin).

Endpoints:

```
POST /merchant-api/{merchant_id}/checkout-sessions
GET  /merchant-api/{merchant_id}/{session_id}
```

---

## Create Checkout Session

```php
$session = $pk->sessions()->create($merchantId, [
  'amount' => 1999,
  'currency' => 'USD',
  'description' => 'Order #1001',
  'source' => 'custom_php'
]);

$sessionId = $session['session_id'];
```

---

## Verify Checkout Session

```php
$status = $pk->sessions()->verify($merchantId, $sessionId);
```

---

# Laravel Usage

### .env

```env
PKPAYPLUS_BASE_URL=https://api.pkpayplus.com
PKPAYPLUS_SECRET_KEY=sk_live_xxx
```

### config/services.php

```php
'pkpayplus' => [
    'base_url' => env('PKPAYPLUS_BASE_URL'),
    'secret_key' => env('PKPAYPLUS_SECRET_KEY'),
],
```

### Example Usage

```php
$pk = new \PkPayPlus\Client(config('services.pkpayplus'));

$products = $pk->products()->list();
```

---

# Testing

The SDK includes:

- Unit tests (mocked HTTP)
- Integration tests (real API)

---

## Run Unit Tests

```bash
./vendor/bin/phpunit
```

---

## Integration Tests (Real API)

Integration tests are skipped unless enabled.

Add to `.env`:

```env
PKPAYPLUS_INTEGRATION=1
PKPAYPLUS_BASE_URL=http://localhost:8000
PKPAYPLUS_SECRET_KEY=sk_test_xxx
```

Run:

```bash
./vendor/bin/phpunit tests/Integration/ProductsIntegrationTest.php --testdox
```

---

# Common Errors

### Missing secret key

Make sure:

```env
PKPAYPLUS_SECRET_KEY=sk_test_xxx
```

---

# Security Notes

- Never expose secret key to frontend
- Use publishable key for client-side checkout
- Use secret key only in backend

---

# License

MIT
