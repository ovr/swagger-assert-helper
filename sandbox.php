<?php

include_once __DIR__ . '/vendor/autoload.php';

/** @var \Swagger\Annotations\Swagger $swagger */
$swagger = \Swagger\scan(
    [
        __DIR__ . '/examples/api/controller',
        __DIR__ . '/examples/api/model'
    ]
);


$wrapper = new \Ovr\Swagger\SwaggerWrapper($swagger);
$operation = $wrapper->getOperationByName('getUserById');

dump($operation);

$response = new \Symfony\Component\HttpFoundation\JsonResponse();
$response->setData(['data' => (object) ['id' => 1, 'name' => 'Test']]);

$wrapper->assertHttpResponseForOperation($response, $operation);
$wrapper->assertHttpResponseForOperation($response, $operation);
