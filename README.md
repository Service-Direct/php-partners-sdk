# Marketplace Partners API Instructions + PHP Examples

## Overview:
This README explains the usage of the Service Direct Marketplace API resources.
* The authenticated `/partners/` routes are for Requesting a Bid (Step 1) and Accepting a Bid (Step 2) for a Tracking Number:
  * `POST /partners/request`
  * `POST /partners/request/{request_id}/accept`
* The open routes are for Service Category mapping and finding the highest Cost Per Lead per Zip Code:
  * `GET /resources/service_categories`
  * `GET /cpl/industry/{service-category_id}`

Using this API, a Publisher Partner who generates phone calls and wants to sell those leads to Service Direct can 
request a bid from Service Direct’s clients by submitting a request with a service category and zip code to `POST /partners/request`. 

Then, if our client's bid is satisfactory, the Partner can accept the bid and receive a forwarding tracking phone number to 
route the caller to the winning client by sending a request to `POST /partners/request/{request_id}/accept`.

Additionally, Publisher Partners can use our other supporting endpoints to determine their best integration options:
* What Service Categories do we cover?
  * Send an API request to `GET /resources/service_categories`
* Do you have current coverage in this zip code? What is the maximum CPL available in this zip code?
  * Send an API request to `GET /cpl/industry/{service-category_id}` 

##
## PHP SDK Examples in this Repository:
This repository contains a simple PHP example library for authenticating, connecting and querying each of our main API 
endpoints. If you use PHP in your environments, it is encouraged to clone this library and augment it as needed. If you 
use a different scripting language, examine the logic in this library so that you can build your own.

### Start in the `examples` directory to uncover how the library works.
__Step 1 is Requesting a Bid__ - you submit a Zip Code and Service Category, API returns if there is an available lead Buyer.
* `/examples/request-bid.php`
  * Use the zip code `11111` to create a test request.
  * Or use an actual zip code `90210` to see a real response

__Step 2 is Accepting the Bid__ - you submit a Request ID, the API returns a Tracking Phone Number.
* `/examples/request-accept.php`
  * Use the request id `0` to accept a test request and receive a phone number for testing routing numbers.
  * Calling the test number will direct you to an automated voice mail indicating success.

### Install this library using Composer:
`composer require service-direct/php-partners-sdk`
* See https://packagist.org/packages/service-direct/php-partners-sdk

### Update with your Private Keys in order to authenticate:
Replace the placeholder values with your private Key and Secret in these files:  `/examples/request-bid.php` and 
`/examples/request-accept.php`
```php
use ServiceDirect\Partners\PartnersClient;

$key = '[YOUR_KEY_HERE]';
$secret = '[YOUR_SECRET_HERE]';

$client = new PartnersClient($key, $secret);
```
* Contact [Service Direct](https://servicedirect.com) in order to obtain your Private Keys. 
* These strings must be kept hidden and treated like any other private credentials or passwords.
* You use your Key and Secret values to calculate the *SD-API-Signature header*, which is required in the `/request` and
  `/accept` routes

### Possible API Responses
API Responses can come in several formats.

__Bad requests__ will result in a response of `400` with a message explaining the error, and a numeric error_code:
```http request
400 {"message":"...","error_code":"..."}
```

When a __buyer is not found__ for the `zip code <-> service category` combination,
a `404` HTTP status codes will be returned with information in the data parameter.
```http request
404 {"data":"..."}
```

When a __buyer is found__ for the `zip code <-> service category` combination,
a `200` HTTP status codes will be returned with information in the data parameter.
```http request
200 {"data":"..."}
```

__Note:__ A `404` or `500` response code can be returned when a resource is not found, or an internal error occurs.

##
## Step 1: Submit a Zip Code & Service Category to Request a Current Bid 
The first step in sending a lead to the Service Direct Marketplace is to check the availability of a Buyer for a 
specific Zip Code and Service Category. This is accomplished via a request to the API: 
`https://api.servicedirect.com/partners/request`. 

### Request a Bid - request format
Submit a `POST` request containing authentication Headers and the appropriate data Payload.
* Headers:
  * __SD-API-Nonce__ – a randomly generated string for request uniqueness
    * [See PHP example of Nonce here in SDK.](https://github.com/Service-Direct/php-partners-sdk/blob/9908cd2bd1398c1144ce82894146ff6eeeb4718c/src/PartnersClient.php#L146)
  * __SD-API-Provider__ – your API Key
  * __SD-API-Signature__ – the *signature* of the request.
    * Signature is calculated by running a HMAC hash of sha256 on the concatenated string of your key, the payload, 
    and the nonce; using your API Secret as the hash key. 
    * [You can see an example of this Signature creation here in our PHP SDK documentation.](https://github.com/Service-Direct/php-partners-sdk/blob/master/src/PartnersClient.php#L147)
* Data:
  * __zip_code__ – (string) your lead's Zip Code
  * __service_category__ – (integer) your lead's Service Category
  
Here is an example request for a bid in zip code 01234 for service category Air Conditioning (2) using cURL:
```shell
curl \
  -X POST \
  "https://api.servicedirect.com/partners/request" \
  -H "SD-API-Nonce: [STRING]" \
  -H "SD-API-Provider: [YOUR_KEY]" \
  -H "SD-API-Signature: [CALCULATE SIGNATURE]" \
  -d "{\"zip_code\":\"01234\",\"service_category\":2}"
```

### Response to the Request for a Bid:
If we have a buyer matching the specified Zip Code and Service Category, then you will receive a JSON response containing:
* A boolean `available_buyer` – `true` meaning the request was successful, or `false` meaning no buyer found
* A number `request_id` – containing the request ID
  * the `request_id` is used in the next step - Accepting the Bid and receiving a Tracking Phone Number
* A number `bid` – containing the bid amount (in USD)
* A number `min_duration` *(If Applicable)* – containing the minimum duration in seconds for this call to be payable

An example response (as JSON):
```shell
{
  "available_buyer": true,
  "request_id": 123,
  "bid": 25
  "min_duration": 60
}
```

### Our PHP Examples for Requesting a Bid
This is how our [PHP SDK sends a Test Data example request (11111 = test zip code)](https://github.com/Service-Direct/php-partners-sdk/blob/9908cd2bd1398c1144ce82894146ff6eeeb4718c/examples/request-bid.php#L20):
```php
use ServiceDirect\Partners\ServiceCategories;

/** ServiceDirect\Partners\PartnersClient $client - the client instance */
$requestData = [
    'zip_code' => '11111',
    'service_category' => ServiceCategories::AirConditioning
];
$response = $client->post('request', $requestData);
```

This is an example response confirming an Available Buyer for that Zip Code and Service Category:
```php
# Buyer available, data contains the Buyer's Bid
print_r($response);
```
Will print:
```
Array(
    [data] => Array(
        [available_buyer] => true
        [request_id] => 1
        [bid] => 30
        [min_duration] => 60
    )
)
```

__Note:__ If we do not have a matching buyer, then we will respond with a `404` coded response with data 
explaining we do not have a buyer:
```php
# Buyer not available
print_r($response);
```
Will print:
```
Array(
    [data] => Array(
        [available_buyer] => false
        [request_id] => 2
    )
)
```

### Testing data for Step 1
In order to test your application, use the zip code `11111` to create a test request. Our system will respond with 
`available_buyer = true` and `request_id = 0` like this:
```shell
{
  "available_buyer": true,
  "request_id": 0,
  "bid": 100
}
```

##
## Step 2: Accept the Bid, Receive a Tracking Phone Number
After receiving a Bid in Step 1, you must make a second request to Accept the Bid and receive a Tracking Phone Number to
route the lead to. 

The Tracking Number directly rings to our Client, the winning Bidder. Our system cycles through Tracking Numbers in a 
rotation, each number is reserved for 60 seconds giving you time to connect to the winning Bidder. 

### Accept a Bid - request format
Submit a `POST` request with authentication headers, using the `request_id` in the URL to
`https://api.servicedirect.com/partners/request/{request_id}/accept`
* Headers:
  * __SD-API-Nonce__
  * __SD-API-Provider__
  * __SD-API-Signature__

Here is an example request accepting a bid using cURL:
```shell
curl \
  -X POST \
  "https://api.servicedirect.com/partners/request/[REQUEST_ID]/accept" \
  -H "SD-API-Nonce: [STRING]" \
  -H "SD-API-Provider: [YOUR_KEY]" \
  -H "SD-API-Signature: [CALCULATED SIGNATURE]" \
```
### Response to Accepting a Bid request:
The response will contain a string `phone_number`, the Tracking Phone Number reserved for you to connect directly 
to our Client with the highest bid.

Example response (as JSON)
```shell
{
  "data": {
    "phone_number": "5555555555"
  }
}
```
#### Our PHP Examples for Accepting a Bid
This is how our [PHP SDK sends a request to Accept a Bid](https://github.com/Service-Direct/php-partners-sdk/blob/9908cd2bd1398c1144ce82894146ff6eeeb4718c/examples/request-accept.php#L21).
```php
/** ServiceDirect\Partners\PartnersClient $client = the client instance */
/** int $requestId = the request id in the response form the Request Bid route */
$response = $client->post("request/{$requestId}/accept");
```

Example PHP response with Tracking Phone Number:
```php
print_r($response);
```
Will print:
```
Array(
    [data] => Array(
        [phone_number] => "5555555555"
    )
)
```

### Testing data for Step 2
In order to test your application, you can use `request_id = 0`, our system will respond with a Test Phone Number that 
you can route test calls to like this:
```shell
{
  "phone_number": "5129566629"
}
```
* The number answers with a automated voice mail indicating success.

##
## Additional API Resources for understanding Service Categories

### See all of our Service Categories
* https://api.servicedirect.com/resources/service_categories
* It returns an array of Service Categories with the ID needed in your requests
* Use this resource to map your Service Categories or Industries to ours
```shell
{
  "data": {
    "service_categories": [
      {
        "id":"177",
        "industry_master_id":"2",
        "name":"Accident Attorney"
      },
      {
        "id":"35",
        "industry_master_id":"4",
        "name":"Accountant"
      },
      {
        "id":"2",
        "industry_master_id":"1",
        "name":"Air Conditioning"
      }
      ...
    ],
    "master_service_categories": [
      /* not used for this SDK */
      ...
    ]
  }
}
```

### See each Service Category's currently available Zip Codes and highest Cost per Lead
This API endpoint returns an array of `zip_codes` with each zip code's `max_cpl`
* `https://api.servicedirect.com/cpl/{service-category_id}`
* Current Air Conditioning data: https://api.servicedirect.com/cpl/industry/2
```shell
{
  "data":
    {
      "industry_name": "Air Conditioning",
      "zip_codes":
        [
          {
            "0":
              {
                "zip_code":"32957",
                "max_cpl":"65.00"
              },
            "1":
              {
                "zip_code":"32958",
                "max_cpl":"65.00"
              },
            "2":
              {
                "zip_code":"32962",
                "max_cpl":"65.00"
              },
            ...
          }
        ]
    }
}
```
## Possible Error Messages and Codes:
* Error code: `1001`
  * Missing the "zip_code" parameter
* Error code: `1002`
  * Missing the "service_category" parameter
* Error code: `1003`
  * The provided "zip_code" could not be found in our system
* Error code: `1004`
  * The provided "service_category_id" is not a valid id or is currently not available. You can see available service 
    categories in the API route: https://api.servicedirect.com/resources/service_categories
* Error code: `1005`
  * No buyer found for the provided zip code and service category
