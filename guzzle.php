<?php


// create http client instance
$client = new Client('http://download.cloud.com/releases');

// create a request
$request = $client->get('/3.0.6/api_3.0.6/TOC_Domain_Admin.html');

// send request / get response
$response = $request->send();

// this is the response body from the requested page (usually html)
$result = $response->getBody();