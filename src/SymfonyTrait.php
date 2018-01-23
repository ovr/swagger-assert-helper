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
                if (array_key_exists($parameter->name, $parameters)) {
                    $parameterValue = $parameters[$parameter->name];
                    if ($parameter->enum && !($options & SwaggerWrapper::SKIP_ENUM_CHECK)) {
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

                    unset($parameters[$parameter->name]);
                } elseif ($parameter->required && !($options & SwaggerWrapper::SKIP_REQUIRED)) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Parameter "%s" is required, please pass value for this in $parameters',
                            $parameter->name
                        )
                    );
                }
            }
        } elseif ($parameters) {
            throw new InvalidArgumentException(
                sprintf(
                    'Operation does not have parameters, but you pass %d parameter(s)',
                    count($parameters)
                )
            );
        }

        foreach ($parameters as $parameter => $value) {
            throw new RuntimeException(
                "Parameter '{$parameter}' passed, but does not exist in request definition (swagger)"
            );
        }

        $request->server->set('REMOTE_ADDR', '8.8.8.8');
        $request->server->set('REQUEST_URI', $path);
        $request->server->set('REQUEST_METHOD', $operation->method);

        return $request;
    }

    /**
     * @param Response $response
     * @return ResponseData
     */
    protected function extractResponseData(Response $response)
    {
        $contentType = $response->headers->get('content-type');
        switch ($contentType) {
            case 'application/json':
                return new ResponseData(
                    $response->getContent(),
                    $response->getStatusCode()
                );
            default:
                throw new RuntimeException("HTTP content-type: {$contentType} does not supported");
        }
    }
}
