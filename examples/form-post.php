<?php

namespace Examples;

use ServiceDirect\Partners\PartnersClient;
use ServiceDirect\Partners\ServiceCategories;

require_once '../vendor/autoload.php';

/**
 * sell a ping request (identified by its lead_token)
 * should include the required data matching to the ping request
 */

$key = '[YOUR_KEY_HERE]';
$secret = '[YOUR_SECRET_HERE]';

$client = new PartnersClient($key, $secret);

// optional fields are indicated by /*?*/
$requestData = [
    'lead_token' => 'd1f986fa781e6496d0fdc63a36c3a14e',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'test@example.com',
    'phone' => '(555) 555-5555',
    'address' => '1100 Congress Ave.',
    'ip' => '127.0.0.1',
    'zip_code' => '78701',
    /*?*/'city' => 'Austin',
    /*?*/'state' => 'TX',
    'service_category' => ServiceCategories::Plumbing,
    /*?*/'subcategory' => 594, /* plumbing subcategory */
    /*?*/'project_description' => 'Need a plumber',
    'tcpa_consent' => true,
    'tcpa_agreement' => 'You agree to the terms',
    'trusted_form_cert_url' => 'https://cert.trustedform.com/TRUSTED_FORM_ID',
    /*?*/'source' => '{internal_identifier}',
    /*?*/'homeowner' => 'yes',
    /*?*/'time_frame' => '2 weeks',
    /*?*/'property_type' => 'residential',
    /*?*/'details' => '[{"q":"What?","a":"Yes"}]',
    /*?*/'test_mode' => false,
];
$response = $client->post('forms/post', $requestData);

echo "Status code: $client->last_http_code\n";
print_r($response);

/*
 * example response:
Array(
    [data] => Array(
        [lead_token] => "d1f986fa781e6496d0fdc63a36c3a14e"
        [success] => 1
    )
    [message] => "Form received successfully."
)
*/
