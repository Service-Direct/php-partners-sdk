<?php

namespace ServiceDirect\Partners;

use Exception;

class PartnersClient
{
    const API_KEY_NAME = 'sd_api_key';

    /** @var string the API root URL */
    private $host = 'https://api.servicedirect.com/partners/';

    /** @var integer timeout default */
    private $timeout = 30;

    /** @var integer connect timeout */
    private $connectTimeout = 30;

    /** @var string the SDK user agent */
    private $useragent = 'servicedirect-sdk-php-v0.2';

    /** @var string the app key */
    private $key;

    /** @var int last call http code */
    public $last_http_code;

    /** @var array last call headers */
    public $last_headers = [];

    /** @var string last call cURL error */
    public $last_error;

    /** @var integer last call cURL error number */
    public $last_error_number;

    /** @var boolean when set to true, the HTTP method functions will return the resource instead of executing it */
    public $returnCurlHandle = false;

    /**
     * @param string $key - the token key
     * @param mixed $placeholder - [deprecated] placeholder for legacy support
     */
    public function __construct($key, $placeholder = null)
    {
        $this->key = $key;
    }

    /**
     * updates the host URL
     * @param string $host - the new host URL
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Perform a POST request to the API
     * @param string $url - the API route
     * @param array $data - [optional] the data to pass to the server
     * @return array the response from the server
     * @throws Exception
     */
    public function post($url, $data = [])
    {
        return $this->http_request('POST', $url, $data);
    }

    /**
     * Perform a GET request to the API
     * @param string $url the API route (including query parameters)
     * @return array the response from the server
     * @throws Exception
     */
    public function get($url)
    {
        return $this->http_request('GET', $url);
    }

    /**
     * Perform a PUT request to the API
     * @param string $url the API route
     * @param array $data the data to pass to the server
     * @return array the response from the server
     * @throws Exception
     */
    public function put($url, $data = [])
    {
        return $this->http_request('PUT', $url, $data);
    }

    /**
     * Perform a DELETE request to the API
     * @param string $url the API route (including query parameters)
     * @return array the response from the server
     * @throws Exception
     */
    public function delete($url)
    {
        return $this->http_request('DELETE', $url);
    }

    /**
     * Inner function that creates the request
     * @param string $method the method of the call
     * @param string $url the API route (including query parameters)
     * @param array $data the data to pass to the server
     * @return array|bool|string|resource the response from the server
     * @throws Exception
     */
    private function http_request($method, $url, $data = [])
    {
        // make sure the method is in upper case for comparison
        $method = strtoupper($method);

        if (strrpos($url, 'https://') !== 0) {
            $url = "$this->host$url";
        }

        if (!is_array($data)) {
            throw new Exception('The $data argument must be an array');
        }

        // set the API key in the request data if not present
        if (!isset($data[self::API_KEY_NAME])) {
            $data[self::API_KEY_NAME] = $this->key;
        }

        $ch = curl_init();

        $headers = [];
        if ($this->methodWithData($method)) {
            $data = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: ' . strlen($data);
        }
        // else, need to append the key to the URL.
        // but we currently only have POST endpoints, which satisfy the "if" check

        curl_setopt_array(
            $ch,
            [
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_USERAGENT => $this->useragent,
                CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
                CURLOPT_TIMEOUT => $this->timeout,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_HEADERFUNCTION => [$this, '_getHeader'],
                CURLOPT_URL => $url
            ]
        );

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                break;

            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;

            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        // when set to true, the HTTP method functions will return the resource instead of executing it
        if ($this->returnCurlHandle) {
            return $ch;
        }

        $response = curl_exec($ch);

        // set the last http code; headers are set automatically by cURL
        $this->last_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $this->last_error = curl_error($ch);
            $this->last_error_number = curl_errno($ch);
        }

        curl_close($ch);

        $result = json_decode($response, true);

        return $result ?: $response;
    }

    /**
     * used by cURL
     * retrieve the returned headers;
     * @param resource $ch passed by the CURLOPT_HEADERFUNCTION function but no used in our case
     * @param string $header the full (key and value) header value; e.g. "Content-Type: application/json"
     * @return int
     */
    public function _getHeader($ch, $header)
    {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->last_headers[$key] = $value;
        }

        return strlen($header);
    }

    /**
     * checks if the method can hold data
     * @param string $method the HTTP method in question
     * @return bool true if the HTTP method can hold data
     */
    private function methodWithData($method)
    {
        return in_array($method, ['POST', 'PUT']);
    }
}
