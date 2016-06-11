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
            if ($path->operationId == $operationId) {
                return $path;
            }
        }

        return null;
    }


    /**
     * @param $operationId
     * @param string $method
     * @return null|\Swagger\Annotations\Operation
     */
    public function getOperationByName($operationId, $method = 'get')
    {
        /** @var \Swagger\Annotations\Path $path */
        foreach ($this->swagger->paths as $path) {
            /** @var \Swagger\Annotations\Operation $operation */
            $operation = $path->{$method};
            if ($operation && $operation->operationId == $operationId) {
                return $operation;
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @return null|\Swagger\Annotations\Definition
     */
    public function getSchemeByName($name)
    {
        if ($this->swagger->definitions) {
            foreach ($this->swagger->definitions as $definition) {
                if (strpos($name, $definition->definition) !== false) {
                    return $definition;
                }
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
                    if ($response->schema) {
                        $scheme = null;

                        if ($response->schema->ref) {
                            $scheme = $this->getSchemeByName($response->schema->ref);
                        }

                        if ($scheme) {
                            if ($scheme->required) {
                            }
                        }
                    }

                    $allFailed = false;
                }
            }
        }

        return $allFailed;
    }
}
