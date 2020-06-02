<?php

namespace Examples;

use ServiceDirect\Partners\PartnersClient;
use ServiceDirect\Partners\ServiceCategories;

require_once '../vendor/autoload.php';

$key = '[YOUR_KEY_HERE]';
$secret = '[YOUR_SECRET_HERE]';

$client = new PartnersClient($key, $secret, true);

$requestData = [
    'zip_code' => 12345,
    'service_category' => ServiceCategories::AirConditioning
];
$response = $client->post('request', $requestData);

print_r($response);

/*
 * example response:
Array(
    [data] => Array(
        [available_buyer] => 1
        [request_id] => 1
        [bid] => 30
        [min_duration] => 60
    )
)
*/
