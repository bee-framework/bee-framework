<?php
require_once '../vendor/autoload.php';
session_start();

$requestBuilder = new \Bee\MVC\Redirect\RedirectedRequestBuilder();
$request = $requestBuilder->buildRequestObject();

var_dump($_REQUEST);
var_dump($request);