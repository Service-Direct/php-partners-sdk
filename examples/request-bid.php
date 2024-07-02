<?php

namespace Examples;

use ServiceDirect\Partners\PartnersClient;
use ServiceDirect\Partners\ServiceCategories;

require_once '../vendor/autoload.php';

/**
 * create a phone request for a specific zip code and service category
 * use the returned request_id for the next leg of the integration - request-accept
 */

$key = '[YOUR_KEY_HERE]';

$client = new PartnersClient($key);

// optional fields are indicated by /*?*/
$requestData = [
    /*?*/'test_mode' => true,
    'zip_code' => '78701',
    'service_category' => ServiceCategories::AirConditioning,
    /*?*/'source' => '{internal_identifier}',
];
$response = $client->post('request', $requestData);

echo "Status code: $client->last_http_code\n";
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
