<?php

namespace Examples;

use ServiceDirect\Partners\PartnersClient;

require_once '../vendor/autoload.php';

/**
 * uses the request_id from the previous leg of the integration - request-bid
 * this leg gets a phone number after accepting the bid
 */

$key = '[YOUR_KEY_HERE]';
$secret = '[YOUR_SECRET_HERE]';

$client = new PartnersClient($key, $secret);

/** @var int $requestId - the request id received from the /request route (see /examples/request-bid.php) */
$requestId = 0;
$response = $client->post("request/{$requestId}/accept");

echo "Status code: $client->last_http_code\n";
print_r($response);

/*
 * example response:
Array(
    [data] => Array(
        [phone_number] => "5555555555"
    )
)
*/
