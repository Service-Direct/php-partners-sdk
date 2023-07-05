# Marketplace Partners API Instructions + PHP Examples

## Overview:
The purpose of this SDK it to ease the integration process for our affiliates who use PHP.

See full documentation for our APIs here:
* [Marketplace Phones API](https://docs.google.com/document/d/e/2PACX-1vSqTEv-aynxXNOSqj9xD4nwMrC9gfl-kE9J0UFWFNtrJcXIE7NE0l3tUH4Zro3cTHwXHmoe_JaHCmTW/pub)
* [Marketplace Forms API](https://docs.google.com/document/u/1/d/e/2PACX-1vQ35nE839QVD2EkkVnhRNhu_hf8NXRJL3gUzn4jdMoAclIX-faGk-nGHJ0_Zchdd0lUMwdJyXk3RLD0/pub)

### Install this library using Composer:
`composer require service-direct/php-partners-sdk`
* See https://packagist.org/packages/service-direct/php-partners-sdk

### Response Codes
The API responds with different HTTP status codes to better rely the result of a call.
These response codes include 200, 400, 404, etc.
If your system is senstive to different response codes, the `suppress_response_codes` variable
can be added to every response URL (e.g. `/partners/request?suppress_response_codes`) to always
return a **200** HTTP status code.

The intended HTTP status code will be returned in the **x-original-status-code** header.

### Endpoints
The SDK integrates with the following API endpoints:

* Phones
  * `POST /partners/request` - request a phone bid
  * `POST /partners/request/{request_id}/accept` - accept a phone bid
    * **This API endpoint should only be requested if the phone number is intended to be called.**

* Forms
  * `POST /partners/forms/ping` - request a form bid
  * `POST /partners/forms/{lead_token}/post` - accept a form bid
  * `POST /partners/forms/{lead_token}/sold_price` - report a lost bid winning price

As well as the following dynamically changing open endpoints to get Service Direct's latest availabilities:

* Service Category mapping; the response includes the service_category_id that 
  * Phones: https://api.servicedirect.com/resources/service_categories?is_marketplace=1
  * Forms: https://api.servicedirect.com/resources/service_categories?is_marketplace=1&is_mp_forms=1
* Find the highest possible Payout per Zip Code:
  * `https://api.servicedirect.com/cpl/industry/{service_category_id}`
    * e.g. https://api.servicedirect.com/cpl/industry/2
  * Where **{service_category_id}** is replaced with an id from the mapping endpoints

## Examples
The SDK includes 5 example files to help facilitate your integration.
In the example file, replace the placeholder value with your private Key to start testing.
```php
use ServiceDirect\Partners\PartnersClient;

$key = '[YOUR_KEY_HERE]';

$client = new PartnersClient($key);
```
* Contact [Service Direct](https://servicedirect.com) in order to obtain your Private Key.
* This string must be kept hidden and treated like any other private credentials or passwords.
* Your Key is **required** in the Phones and Forms endpoints
* In all the example files, the optional value of **test_mode** is set to **true**

## Phones
Using this SDK, a Publisher Partner who generates phone calls and wants to sell those leads to Service Direct can
request a bid from Service Direct’s clients by submitting a request with a service category and zip code to
`POST /partners/request`.

See [1] `examples/request-bid.php`

Then, if our client's bid is satisfactory, the Partner can accept the bid and receive a forwarding tracking phone number
to route the caller to the winning client by sending a request to `POST /partners/request/{request_id}/accept`.

See [2] `examples/request-accept.php`

## Forms
Using this API, a Publisher Partner who generates form leads and wants to sell those leads to Service Direct can
request a bid from Service Direct's clients by submitting a request with a service category, zip code, and a
TCPA consent to `POST /partners/forms/ping`.

See [3] `examples/form-ping.php`

Then, if our client's bid is satisfactory, the Partner can accept the bid and receive a confirmation message by
sending a request to `POST /partners/forms/{lead_token}/post`.

See [4] `examples/form-post.php`

If our client's bid is unsatisfactory, the Partner can report the winning bid and receive a confirmation message by
sending a request to `POST /partners/forms/{lead_token}/post`.

See [5] `examples/form-sold-price.php`

## General
Publisher Partners can use our supporting endpoints to determine their best integration options:
* What Service Categories do we cover?
* Phone: https://api.servicedirect.com/resources/service_categories?is_marketplace=1
* Forms: https://api.servicedirect.com/resources/service_categories?is_marketplace=1&is_mp_forms=1
You can open these links directly in your browser to view the response

The response (for both options) will be in the following format:
```json
{
  "data": {
    "service_categories": [
      {
        "id":"20",
        "industry_master_id":"4",
        "name":"Plumbing"
      },
      {
        "id":"2",
        "industry_master_id":"1",
        "name":"Air Conditioning"
      },
      ...
    ],
    "master_service_categories": [
      /* not for this SDK purposes */
      ...
    ]
  }
}
```

* Do you have current coverage in this zip code? What is the maximum CPL available in this zip code?
  * Send an API request to `https://api.servicedirect.com/cpl/industry/{service_category_id}`

Response for service category **2** - Air Conditioning:
```json
{
  "data": {
    "industry_name": "Air Conditioning",
    "zip_codes": [
      {
        "zip_code":"32957",
        "max_cpl":"65.00"
      },
      {
        "zip_code":"32958",
        "max_cpl":"65.00"
      },
      {
        "zip_code":"32962",
        "max_cpl":"65.00"
      },
      ...
    ]
  }
}
```
Fur further assistance please contact support@servicedirect.com
