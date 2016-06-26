<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Ovr\Swagger;

use InvalidArgumentException;
use RuntimeException;
use Swagger\Annotations\Operation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait SymfonyTrait
{
    /**
     * Prepare a Symfony Request by Operation with $parameters
     *
     * @param Operation $operation
     * @param array $parameters
     * @param int $options BitMask of options to skip or something else
     * @return Request
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function makeRequestByOperation(Operation $operation, array $parameters = [], $options = 0)
    {
        $request = new Request();

        $path = $operation->path;

        if ($operation->parameters) {
            foreach ($operation->parameters as $parameter) {
                if (isset($parameters[$parameter->name])) {
                    $parameterValue = $parameters[$parameter->name];
                    if ($parameter->enum && !($operation & SwaggerWrapper::SKIP_ENUM_CHECK)) {
                        if (!in_array($parameterValue, $parameter->enum)) {
                            throw new InvalidArgumentException(
                                sprintf(
                                    'Parameter "%s" has enum {"%s"}, but value "%s"',
                                    $parameter->name,
                                    implode('"|"', $parameter->enum),
                                    $parameterValue
                                )
                            );
                        }
                    }

                    switch ($parameter->in) {
                        case 'path':
                            $path = str_replace('{' . $parameter->name . '}', $parameterValue, $path);
                            break;
                        case 'header':
                            $request->headers->set($parameter->name, $parameterValue);
                            break;
                        case 'query':
                            $request->query->set($parameter->name, $parameterValue);
                            break;
                        case 'formData':
                            $request->request->set($parameter->name, $parameterValue);
                            break;
                        default:
                            throw new RuntimeException(
                                sprintf(
                                    'Parameter "%s" with ->in = "%s" is not supported',
                                    $parameter->parameter,
                                    $parameter->in
                                )
                            );
                    }
                } elseif ($parameter->required && !($operation & SwaggerWrapper::SKIP_REQUIRED)) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Parameter "%s" is required, please pass value for this in $parameters',
                            $parameter->name
                        )
                    );
                }
            }
        }

        $request->server->set('REQUEST_URI', $path);
        $request->server->set('REQUEST_METHOD', $operation->method);

        return $request;
    }

    public function assertHttpResponseForOperation(Response $response, Operation $operation)
    {
        $contentType = $response->headers->get('content-type');
        switch ($contentType) {
            case 'application/json':
                break;
            default:
                throw new RuntimeException('Content type, ' . $contentType . ' is not supported');
        }
    }
}
