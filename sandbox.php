<?php

include_once __DIR__ . '/vendor/autoload.php';

/** @var \Swagger\Annotations\Swagger $swagger */
$swagger = \Swagger\scan(
    [
        __DIR__ . '/examples/api/'
    ]
);


$wrapper = new \Ovr\Swagger\SwaggerWrapper($swagger);
$path = $wrapper->getPathByName('getUserById');

dump($path);
