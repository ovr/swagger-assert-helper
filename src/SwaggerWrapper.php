<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Ovr\Swagger;

use Flow\JSONPath\JSONPath;
use RuntimeException;
use Swagger\Annotations\Response as SwaggerResponse;
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
                $operation->path = $this->swagger->basePath . $operation->path;

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

    /**
     * @param Response $httpResponse
     * @param \Swagger\Annotations\Operation $path
     * @return bool
     */
    public function assertHttpResponseForOperation(Response $httpResponse, \Swagger\Annotations\Operation $path)
    {
        $allFailed = true;

        if ($path->responses) {
            /** @var SwaggerResponse $response */
            foreach ($path->responses as $response) {
                if ($response->response == $httpResponse->getStatusCode()) {
                    if ($response->schema) {
                        /** @var \Swagger\Annotations\Definition|null $scheme */
                        $scheme = null;

                        if ($response->schema->ref) {
                            $scheme = $this->getSchemeByName($response->schema->ref);
                        }

                        $jsonPath = (new JSONPath(json_decode($httpResponse->getContent())));
                        $this->validateScheme($scheme, $jsonPath);
                    }

                    $allFailed = false;
                }
            }
        }

        return $allFailed;
    }

    /**
     * @param Response $httpResponse
     * @param SwaggerResponse $response
     */
    public function assertHttpResponseForOperationResponse(Response $httpResponse, SwaggerResponse $response)
    {
        if ($response->response == $httpResponse->getStatusCode()) {
            if ($response->schema) {
                /** @var \Swagger\Annotations\Definition|null $scheme */
                $scheme = null;

                if ($response->schema->ref) {
                    $scheme = $this->getSchemeByName($response->schema->ref);
                }

                $jsonPath = (new JSONPath(json_decode($httpResponse->getContent())));
                $this->validateScheme($scheme, $jsonPath);
            }
        } else {
            throw new RuntimeException(
                sprintf(
                    'Response code is not valid, expected: "%s", actual: "%s"',
                    $response->response,
                    $httpResponse->getStatusCode()
                )
            );
        }
    }

    /**
     * @param \Swagger\Annotations\Definition $scheme
     * @param JSONPath $jsonPath
     */
    protected function validateScheme(\Swagger\Annotations\Definition $scheme, JSONPath $jsonPath)
    {
        if ($scheme->required) {
            foreach ($scheme->required as $requiredPropertyName) {
                foreach ($scheme->properties as $property) {
                    if ($property->property == $requiredPropertyName) {
                        $property->required = true;
                    }
                }
            }
        }

        /** @var \Swagger\Annotations\Property $property */
        foreach ($scheme->properties as $property) {
            $value = $jsonPath->find('$..' . $property->property);
            if (!$value->valid()) {
                if ($property->required) {
                    throw new RuntimeException(
                        sprintf(
                            'Cannot find property "%s" in json',
                            $property->property
                        )
                    );
                } else {
                    continue;
                }
            }

            if ($property->items) {
                $scheme = $this->getSchemeByName($property->items);
                $this->validateScheme($scheme, $jsonPath->find('$..' . $property->property));
            }

            $this->validateProperty($property, current($value->data()));
        }
    }

    /**
     * @param \Swagger\Annotations\Property $property
     * @param $value
     */
    protected function validateProperty(\Swagger\Annotations\Property $property, $value)
    {
        switch ($property->type) {
            case 'string':
            case 'boolean':
            case 'integer':
                if (gettype($value) != $property->type && $property->required) {
                    throw new RuntimeException(
                        sprintf(
                            'Type of the property %s must be %s instead of %s',
                            $property->property,
                            $property->type,
                            gettype($value)
                        )
                    );
                }
                break;
            case 'number':
                if (gettype($value) != 'double' && $property->required) {
                    throw new RuntimeException(
                        sprintf(
                            'Type of the property %s must be %s instead of %s',
                            $property->property,
                            $property->type,
                            gettype($value)
                        )
                    );
                }
        }
    }
}
