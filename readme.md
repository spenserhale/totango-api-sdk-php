# Totango API SDK (PHP)

This is a PHP SDK for the Totango API. It provides a simple way to interact with the Totango API.

## Installation

```bash
composer require spenserhale/totango-api-sdk
```
## Example Usage

```php
$client = new \SH\Totango\TotangoCurlClient("<YOUR_TOTANGO_AUTH_TOKEN>");

$api = new \SH\Totango\TotangoSearchApi($client);

$accounts = $api->accounts(
    [
        [
            "type"      => "string_attribute",
            "attribute" => "Account Type",
            "in_list"   => ["Parent", "Child"]
        ],
    ],
    [
        [
            "type" => "string",
            "term" => "health",
        ],
    ]
);
```
