# php-partners-sdk

A PHP library for integration with Service Direct's Partners API.

Install the SDK using composer:
`composer require service-direct/php-partners-sdk`

Things you can do with this API:

* Use the `examples\request-bid.php` file to tell Service Direct about phone leads of a certain
  service category (e.g. Plumbing, Air Conditioning, etc...), and see whether Service Direct is
  interested in bidding on your lead.
* Use the `examples\request-aquire.php` file if you accept our bid and want to forward us the call.
  We'll respond with a phone number for you to forward the call to.

See the `examples` directory for the usage.

* Use the zip code `11111` to create a test request.

* Use the request id `0` to accept a test request.\
Accepting the request id 0 will return a phone number for testing routing numbers.\
Calling this number will direct you to an automated voice mail indicating success.

## Instantiating the client
```php
$key = '[YOUR_KEY_HERE]';
$secret = '[YOUR_SECRET_HERE]';

$client = new PartnersClient($key, $secret);
```
Replace the `[YOUR_KEY_HERE]` and `[YOUR_SECRET_HERE]` strings with your token key secret provided by Service Direct

#### Responses
Responses can come in several formats

Bad requests will result in a response of `400` with a message explaining the error: 
```http request
400 {"message":"..."}
```
When a buyer is not found for the `zip code <-> service category` combination,
a `404` HTTP status codes will be returned with information in the data parameter
```http request
404 {"data":"..."}
```
When a buyer is found for the `zip code <-> service category` combination,
a `200` HTTP status codes will be returned with information in the data parameter
```http request
200 {"data":"..."}
```

\* A `404` or `500` response codes can be returned when a resource is not found or an internal error occurrs. 

## Request bid
Example of a request with the test data (Zip Code of 11111):
```php
/** ServiceDirect\Partners\PartnersClient $client - the client instance */
$requestData = [
    'zip_code' => 11111,
    'service_category' => ServiceCategories::AirConditioning
];
$response = $client->post('request', $requestData);
```

Example of a positive response:
```php
print_r($response);
Array(
    [data] => Array(
        [available_buyer] => 1
        [request_id] => 1
        [bid] => 30
        [min_duration] => 60
    )
)
```

## Request accept bid
Example of a request:
```php
/** ServiceDirect\Partners\PartnersClient $client - the client instance */
/** int $requestId - the request id in the response form the Request Bid route */
$response = $client->post("request/{$requestId}/accept");
```

Example of a positive response:
```php
print_r($response);
Array(
    [data] => Array(
        [phone_number] => "5555555555"
    )
)
```