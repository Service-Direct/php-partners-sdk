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
