# Earn API Instructions + PHP Examples

## Overview:
The purpose of this SDK it to ease the integration process for our affiliates who use PHP.

See full documentation for our APIs here:
* [Marketplace Phones API](https://docs.google.com/document/d/e/2PACX-1vSqTEv-aynxXNOSqj9xD4nwMrC9gfl-kE9J0UFWFNtrJcXIE7NE0l3tUH4Zro3cTHwXHmoe_JaHCmTW/pub)

### Install this library using Composer:
`composer require service-direct/php-partners-sdk`
* See https://packagist.org/packages/service-direct/php-partners-sdk

### Response Codes
The API responds with different HTTP status codes to better rely the result of a call.
These response codes include `200`, `400`, `404`, etc.
If your system is senstive to different response codes, the `suppress_response_codes` variable
can be added to every response URL (e.g. `/partners/request?suppress_response_codes`) to always
return a **200** HTTP status code.

The intended HTTP status code will be returned in the **x-original-status-code** header.

### Endpoints
The SDK integrates with the following API Phone endpoints:
* `POST /partners/request` - request a phone bid
* `POST /partners/request/{request_id}/accept` - accept a phone bid
  * **This API endpoint should only be requested if the phone number is intended to be called.**
* `POST /partners/request/{request_id}/sold_price` - report the sold price a different buyer (other than Service Direct) paid

As well as the following dynamically changing open endpoint to get Service Direct's latest supported Service Categories:
* Phones: https://api.servicedirect.com/resources/service_categories?is_marketplace=1


## Examples
The SDK includes 3 example files to help facilitate your integration.
In the example file, replace the placeholder value with your private Key to start testing.
```php
use ServiceDirect\Partners\PartnersClient;

$key = '[YOUR_KEY_HERE]';

$client = new PartnersClient($key);
```
* Contact [Service Direct](https://servicedirect.com) in order to obtain your Private Key.
* This string must be kept hidden and treated like any other private credentials or passwords.
* Your Key is **required** in the Phones endpoints
* In all the example files, the optional value of **test_mode** is set to **true**

## Phones
Using this SDK, a Publisher Partner who generates phone calls and wants to sell those leads to Service Direct can
request a bid from Service Direct’s clients by submitting a request with a service category and zip code to
`POST /partners/request`.

See [1] `examples/request-bid.php`

Then, if our client's bid is satisfactory, the Partner can accept the bid and receive a forwarding tracking phone number
to route the caller to the winning client by sending a request to `POST /partners/request/{request_id}/accept`.

See [2] `examples/request-accept.php`

If, however, a different buy (other than Service Direct) won your lead, you can notify Service Direct of the sold price
by sending a request to `POST /partners/request/{request_id}/sold_price`.
This process helps us better understand the market value of your leads and adjust our bids accordingly.

See [3] `examples/request-sold-price.php`

## General
Publisher Partners can use our supporting endpoint to determine their best integration options:
* What Service Categories do we cover?
* https://api.servicedirect.com/resources/service_categories?is_marketplace=1
You can open these links directly in your browser to view the response

The response will be in the following format:
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
      /* not for the purpose of this SDK */
      ...
    ]
  }
}
```

For further assistance please contact [support@servicedirect.com](mailto:support@servicedirect.com)
