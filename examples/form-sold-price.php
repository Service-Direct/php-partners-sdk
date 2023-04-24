<?php

namespace Examples;

use ServiceDirect\Partners\PartnersClient;

require_once '../vendor/autoload.php';

/**
 * report a losing bid (ping) sold price based on its lead_token
 */

$key = '[YOUR_KEY_HERE]';

$client = new PartnersClient($key);

// optional fields are indicated by /*?*/
$requestData = [
    /*?*/'test_mode' => true,
    'lead_token' => 'd1f986fa781e6496d0fdc63a36c3a14e',
    'sold_price' => 30,
];
$response = $client->post('forms/sold_price', $requestData);

echo "Status code: $client->last_http_code\n";
print_r($response);

/*
 * example response:
Array(
    [success] => 1
    [message] => "Sold price received successfully"
)
*/
