<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Ovr\Swagger;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SwaggerWrapper
{
    /**
     * @var \Swagger\Annotations\Swagger
     */
    protected $swagger;

    public function __construct(\Swagger\Annotations\Swagger $swagger)
    {
        $this->swagger = $swagger;
    }

    /**
     * @param $operationId
     * @return \Swagger\Annotations\Path|null
     */
    public function getPathByName($operationId)
    {
        foreach ($this->swagger->paths as $path) {
            if ($path->operationId = $operationId) {
                return $path;
            }
        }

        return null;
    }


    /**
     * @param $operationId
     * @return \Swagger\Annotations\Operation|null
     */
    public function getOperationByName($operationId)
    {
        foreach ($this->swagger->paths as $path) {
            if ($path->operationId = $operationId) {
                return $path->get;
            }
        }

        return null;
    }

    public function assertHttpResponseForOperation(Response $httpResponse, \Swagger\Annotations\Operation $path)
    {
        $allFailed = true;

        if ($path->responses) {
            /** @var \Swagger\Annotations\Response $response */
            foreach ($path->responses as $response) {
                if ($response->response == $httpResponse->getStatusCode()) {
                    $allFailed = false;
                }
            }
        }

        return $allFailed;
    }
}
