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
    /*?*/'test_mode' => true, // remove in production
    'zip_code' => '78701',
    'service_category' => ServiceCategories::AirConditioning,
//    /*?*/'source' => '{SOURCE_AKA_SUB_ID}',
//    /*?*/'caller_id' => '{CALLER_ID}',
//    /*?*/'caller_id_in_post' => false,
//    /*?*/'tracking_id' => '{INTERNAL_TRACKING_ID}',
//    /*?*/'suppress_response_codes' => true,
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
        [tracking_id] => "<provided tracking id>" // present only when provided in the request
        [test_mode] => true // present only in test mode
    )
)
*/
