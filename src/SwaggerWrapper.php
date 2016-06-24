<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Ovr\Swagger;

use Flow\JSONPath\JSONPath;
use RuntimeException;
use Swagger\Annotations\Definition;
use Swagger\Annotations\Operation;
use Swagger\Annotations\Property;
use Swagger\Annotations\Response as SwaggerResponse;
use Symfony\Component\HttpFoundation\Response;

class SwaggerWrapper extends \PHPUnit_Framework_Assert
{
    /**
     * @var \Swagger\Annotations\Swagger
     */
    protected $swagger;

    protected static $possiblePathMethods = [
        'get',
        'post',
        'put',
        'delete',
        'head',
        'patch'
    ];

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
     * @param string $operationId
     * @param string|null $method Will be removed in feature
     * @return null|Operation
     */
    public function getOperationByName($operationId, $method = null)
    {
        if ($method) {
            /** @var \Swagger\Annotations\Path $path */
            foreach ($this->swagger->paths as $path) {
                /** @var Operation|null $operation */
                $operation = $path->{$method};
                if ($operation && $operation->operationId == $operationId) {
                    $operation->path = $this->swagger->basePath . $operation->path;

                    return $operation;
                }
            }
        } else {
            /** @var \Swagger\Annotations\Path $path */
            foreach ($this->swagger->paths as $path) {
                foreach (self::$possiblePathMethods as $possiblePathMethod) {
                    /** @var Operation|null $operation */
                    $operation = $path->{$possiblePathMethod};
                    if ($operation && $operation->operationId == $operationId) {
                        $operation->path = $this->swagger->basePath . $operation->path;

                        return $operation;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @return null|Definition
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
     * @param Operation $path
     * @param int $statusCode
     * @return null|SwaggerResponse
     */
    public function findResponseByStatusCode(Operation $path, $statusCode = 200)
    {
        if ($path->responses) {
            foreach ($path->responses as $response) {
                if ($response->response == $statusCode) {
                    return $response;
                }
            }
        }

        return null;
    }

    /**
     * @param Response $httpResponse
     * @param Operation $path
     * @param int $statusCode
     * @throws \RuntimeException
     */
    public function assertHttpResponseForOperation(Response $httpResponse, Operation $path, $statusCode = 200)
    {
        $response = $this->findResponseByStatusCode($path, $statusCode);
        if ($response) {
            parent::assertEquals(
                $statusCode,
                $httpResponse->getStatusCode(),
                'HTTP Response Code must equals with ' . $statusCode
            );

            if ($response->schema) {
                /** @var Definition|null $scheme */
                $scheme = null;

                if ($response->schema->ref) {
                    $scheme = $this->getSchemeByName($response->schema->ref);
                }

                $jsonPath = (new JSONPath(json_decode($httpResponse->getContent())));
                $this->validateScheme($scheme, $jsonPath);
            }

            return;
        }

        throw new RuntimeException('Cannot find Response in Operation for ' . $statusCode . ' status code');
    }

    /**
     * @param Response $httpResponse
     * @param SwaggerResponse $response
     */
    public function assertHttpResponseForOperationResponse(Response $httpResponse, SwaggerResponse $response)
    {
        parent::assertEquals(
            $response->response,
            $httpResponse->getStatusCode(),
            'HTTP Response Code must equals with ' . $response->response
        );

        if ($response->schema) {
            /** @var Definition|null $scheme */
            $scheme = null;

            if ($response->schema->ref) {
                $scheme = $this->getSchemeByName($response->schema->ref);
            }

            $jsonPath = (new JSONPath(json_decode($httpResponse->getContent())));
            $this->validateScheme($scheme, $jsonPath);
        }
    }

    /**
     * @param Definition $scheme
     */
    public function flagPropertyAsRequiredFromDefinition(Definition $scheme)
    {
        if ($scheme->required) {
            foreach ($scheme->required as $requiredPropertyName) {
                $find = false;

                foreach ($scheme->properties as $property) {
                    if ($property->property == $requiredPropertyName) {
                        $find = true;
                        $property->required = true;

                        break;
                    }
                }

                parent::assertTrue(
                    $find,
                    sprintf(
                        'Cannot find property with name %s to mark it as required, on scheme %s',
                        $requiredPropertyName,
                        $scheme->definition
                    )
                );
            }
        }
    }

    /**
     * @param Definition $scheme
     * @param JSONPath $jsonPath
     */
    protected function validateScheme(Definition $scheme, JSONPath $jsonPath)
    {
        $this->flagPropertyAsRequiredFromDefinition($scheme);

        /** @var Property $property */
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
     * @param Property $property
     * @param $value
     * @throws RuntimeException
     */
    protected function validateProperty(Property $property, $value)
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
                } elseif (!$this->checkFormat($property, $value)) {
                    throw new RuntimeException(
                        sprintf(
                            'Format of the "%s" property (value "%s") is not valid, need "%s" format',
                            $property->property,
                            $value,
                            $property->format
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

    /**
     * @param Property $property
     * @param $value
     * @return bool
     */
    protected function checkFormat(Property $property, $value)
    {
        if ($property->format) {
            switch ($property->format) {
                case 'date':
                    $parsedDate = date_parse($value);
                    return (
                        $parsedDate
                        && $parsedDate['year'] !== false
                        && $parsedDate['month'] !== false
                        && $parsedDate['day'] !== false
                    );
                case 'date-time':
                    $parsedDate = date_parse($value);
                    return (
                        $parsedDate
                        && $parsedDate['hour'] !== false
                        && $parsedDate['minute'] !== false
                        && $parsedDate['second'] !== false
                    );
            }
        }

        return true;
    }
}
