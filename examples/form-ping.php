<?php

namespace Examples;

use ServiceDirect\Partners\PartnersClient;
use ServiceDirect\Partners\ServiceCategories;

require_once '../vendor/autoload.php';

/**
 * create a form ping request for a specific zip code and service category
 * use the returned lead_token for the next leg of the integration:
 *  - form-post for bidding on the lead
 *  - form-sold-price for losing bid reporting
 */

$key = '[YOUR_KEY_HERE]';

$client = new PartnersClient($key);

// optional fields are indicated by /*?*/
$requestData = [
    /*?*/'test_mode' => true,
    'zip_code' => '78602',
    'service_category' => ServiceCategories::Plumbing,
    /*?*/'subcategory' => 594, /* plumbing subcategory */
    'tcpa_consent' => true,
    /*?*/'source' => '',
    /*?*/'project_description' => 'Need a plumber',
    /*?*/'homeowner' => 'yes',
    /*?*/'time_frame' => '2 weeks',
    /*?*/'property_type' => 'residential',
    /*?*/'ip' => '127.0.0.1',
];
$response = $client->post('forms/ping', $requestData);

echo "Status code: $client->last_http_code\n";
print_r($response);

/*
 * example response:
Array(
    [data] => Array(
        [available_buyer] => 1
        [lead_token] => "d1f986fa781e6496d0fdc63a36c3a14e"
        [bid] => 30
    )
)
*/
