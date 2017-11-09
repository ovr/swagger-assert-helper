<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Ovr\Swagger;

use Flow\JSONPath\JSONPath;
use RuntimeException;
use Swagger\Annotations\Definition;
use Swagger\Annotations\Operation;
use Swagger\Annotations\Parameter;
use Swagger\Annotations\Property;
use Swagger\Annotations\Response as SwaggerResponse;
use Symfony\Component\HttpFoundation\Response;

class SwaggerWrapper extends \PHPUnit_Framework_Assert
{
    /**
     * Skip Required check on Property
     */
    const SKIP_REQUIRED = 2;

    /**
     * Skip Enum check on Property
     */
    const SKIP_ENUM_CHECK = 4;

    /**
     * @var \Swagger\Annotations\Swagger
     */
    protected $swagger;

    protected static $possiblePathMethods = [
        'get',
        'post',
        'put',
        'delete',
        'options',
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
     * @param Operation $operation
     * @return Operation
     */
    protected function prepareOperation(Operation $operation)
    {
        /**
         * @todo Prepare operations on SwaggerWrapper __construct?
         *
         * Object is passed by referenced variable
         * and after 2 operations get, operation will be broken
         * think a little bit about this moment
         */
        $operation = clone $operation;
        $operation->path = $this->swagger->basePath . $operation->path;

        /**
         * I don't known How will be better
         * But I am going to add a new required Parameter to check this in Request Scheme
         */
        if ($operation->security) {
            foreach ($operation->security as $security) {
                parent::assertInternalType(
                    'array',
                    $security,
                    'Operation->security must be array of objects'
                );

                $name = key($security);

                $securityDefinition = $this->getSecurityByName($name);
                parent::assertInternalType(
                    'object',
                    $securityDefinition,
                    "Unknown security definition {$name}"
                );

                $operation->parameters[] = new Parameter(
                    [
                        'name' => $securityDefinition->name,
                        'in' => $securityDefinition->in,
                        'description' => $securityDefinition->description,
                        'required' => true
                    ]
                );
            }
        }

        return $operation;
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
                    return $this->prepareOperation($operation);
                }
            }
        } else {
            /** @var \Swagger\Annotations\Path $path */
            foreach ($this->swagger->paths as $path) {
                foreach (self::$possiblePathMethods as $possiblePathMethod) {
                    /** @var Operation|null $operation */
                    $operation = $path->{$possiblePathMethod};
                    if ($operation && $operation->operationId == $operationId) {
                        return $this->prepareOperation($operation);
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @return Definition|null
     */
    public function getSchemeByName($name)
    {
        if ($this->swagger->definitions) {
            if (strpos($name, '#/definitions/') === 0) {
                $name = substr($name, 14);
            }

            foreach ($this->swagger->definitions as $definition) {
                if ($definition->definition == $name) {
                    return $definition;
                }
            }
        }

        return null;
    }

    /**
     * @param $name
     * @return \Swagger\Annotations\SecurityScheme|null
     */
    public function getSecurityByName($name)
    {
        if ($this->swagger->securityDefinitions) {
            foreach ($this->swagger->securityDefinitions as $definition) {
                if ($definition->securityDefinition === $name) {
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
                parent::assertInstanceOf(
                    Definition::class,
                    $scheme,
                    sprintf(
                        'Definition "%s" not found in Swagger Scheme',
                        $response->schema->ref
                    )
                );
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
                foreach ($scheme->properties as $property) {
                    if ($property->property == $requiredPropertyName) {
                        $property->required = true;

                        /**
                         * Go to the next property name from required, via this property is exist
                         */
                        continue 2;
                    }
                }

                parent::assertTrue(
                    false,
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
     * @throws \RuntimeException
     */
    protected function validateScheme(Definition $scheme, JSONPath $jsonPath)
    {
        $this->flagPropertyAsRequiredFromDefinition($scheme);

        /** @var Property $property */
        foreach ($scheme->properties as $property) {
            $value = $jsonPath->find('$.' . $property->property);
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

            $iterable = $this->validateProperty($property, current($value->data()));

            if ($property->items && $property->items->ref) {
                $scheme = $this->getSchemeByName($property->items->ref);
                parent::assertInstanceOf(
                    Definition::class,
                    $scheme,
                    sprintf(
                        'Definition "%s" not found in Swagger Scheme',
                        $property->items->ref
                    )
                );

                if ($iterable) {
                    foreach (current($value->data()) as $entity) {
                        $this->validateScheme($scheme, new JSONPath($entity));
                    }
                }
            }
        }
    }

    /**
     * @param Property $property
     * @param mixed $value
     * @return bool True if it's possible to iterate this property
     * @throws RuntimeException
     */
    protected function validateProperty(Property $property, $value)
    {
        // Supported inside Swagger 3+ for Property, now will be null as default
        $nullable = true;

        if ($nullable === false && $value === null) {
            throw new RuntimeException(
                sprintf(
                    'Property %s is required and cannot be null',
                    $property->property
                )
            );
        }

        switch ($property->type) {
            case 'array':
                if ($property->required) {
                    if (gettype($value) != $property->type) {
                        if ($nullable) {
                            if ($value !== null) {
                                throw new RuntimeException(
                                    sprintf(
                                        'Type of the property %s must be %s|null (because nullable) instead of %s actual',
                                        $property->property,
                                        $property->type,
                                        gettype($value)
                                    )
                                );
                            }
                        } else {
                            throw new RuntimeException(
                                sprintf(
                                    'Type of the property %s must be %s instead of %s actual',
                                    $property->property,
                                    $property->type,
                                    gettype($value)
                                )
                            );
                        }
                    }

                    if ($property->minItems) {
                        if ($property->minItems > count($value)) {
                            throw new RuntimeException(
                                sprintf(
                                    'Defined minItems of the property "%s", expected: %s, actual: %s',
                                    $property->property,
                                    $property->minItems,
                                    count($value)
                                )
                            );
                        }
                    }

                    if ($property->maxItems) {
                        if ($property->maxItems < count($value)) {
                            throw new RuntimeException(
                                sprintf(
                                    'Defined maxItems of the property "%s", expected: %s, actual: %s',
                                    $property->property,
                                    $property->maxItems,
                                    count($value)
                                )
                            );
                        }
                    }

                    return (bool) count($value);
                }
                break;
            case 'boolean':
            case 'string':
            case 'integer':
                if (gettype($value) != $property->type) {
                    if ($nullable) {
                        if ($value !== null) {
                            throw new RuntimeException(
                                sprintf(
                                    'Type of the property %s must be %s|null (because nullable) instead of %s',
                                    $property->property,
                                    $property->type,
                                    gettype($value)
                                )
                            );
                        }
                    } else {
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

                if ($value !== null) {
                    if (!$this->checkFormat($property, $value)) {
                        throw new RuntimeException(
                            sprintf(
                                'Format of the "%s" property (value "%s") is not valid, need "%s" format',
                                $property->property,
                                $value,
                                $property->format
                            )
                        );
                    }

                    if ($property->type == 'integer') {
                        if ($property->minimum) {
                            if ($property->exclusiveMinimum && $value < $property->minimum) {
                                throw new RuntimeException(
                                    sprintf(
                                        'Property "%s" (value "%s") < %s (exclusive minimum)',
                                        $property->property,
                                        $value,
                                        $property->minimum
                                    )
                                );
                            } elseif ($value <= $property->minimum) {
                                throw new RuntimeException(
                                    sprintf(
                                        'Property "%s" (value "%s") <= %s (minimum)',
                                        $property->property,
                                        $value,
                                        $property->minimum
                                    )
                                );
                            }
                        }

                        if ($property->maximum) {
                            if ($property->exclusiveMaximum && $value > $property->maximum) {
                                throw new RuntimeException(
                                    sprintf(
                                        'Property "%s" (value "%s") > %s (exclusive maximum)',
                                        $property->property,
                                        $value,
                                        $property->maximum
                                    )
                                );
                            } elseif ($value >= $property->maximum) {
                                throw new RuntimeException(
                                    sprintf(
                                        'Property "%s" (value "%s") >= %s (maximum)',
                                        $property->property,
                                        $value,
                                        $property->maximum
                                    )
                                );
                            }
                        }
                    }
                }
                break;
            case 'number':
                if (gettype($value) != 'double') {
                    if ($nullable) {
                        if ($value !== null) {
                            throw new RuntimeException(
                                sprintf(
                                    'Type of the property %s must be %s|null (because nullable) instead of %s',
                                    $property->property,
                                    $property->type,
                                    gettype($value)
                                )
                            );
                        }
                    } else {
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

        return false;
    }

    /**
     * @param Property $property
     * @param mixed $value
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
