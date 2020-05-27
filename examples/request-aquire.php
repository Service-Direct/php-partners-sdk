<?php

namespace Examples;

use ServiceDirect\Marketplace\MarketplaceClient;

require_once '../vendor/autoload.php';

$key = '123';
$secret = '456';

$client = new MarketplaceClient($key, $secret, true);

/** @var int $requestId - the request id received from the /request route (see /examples/request-bid.php) */
$requestId;
$response = $client->post("request/{$requestId}/acquire");

print_r($response);

/*
 * example response:
Array(
    [data] => Array(
        [phone_number] => 5555555555
    )
)
*/
