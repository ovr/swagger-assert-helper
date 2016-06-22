<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Ovr\Swagger;

use InvalidArgumentException;
use RuntimeException;
use Slim\Http\Request;
use Swagger\Annotations\Operation;

trait SlimTrait
{
    /**
     * Prepare a Slim Request by Operation with $parameters
     *
     * @param Operation $operation
     * @param array $parameters
     * @param bool $skipRequired
     * @return Request
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function makeRequestByOperation(Operation $operation, array $parameters = [], $skipRequired = false)
    {
        $headers = new \Slim\Http\Headers();
        $body = new \Slim\Http\RequestBody();

        $path = $operation->path;

        if ($operation->parameters) {
            foreach ($operation->parameters as $parameter) {
                if (isset($parameters[$parameter->name])) {
                    switch ($parameter->in) {
                        case 'path':
                            $path = str_replace('{' . $parameter->name . '}', $parameters[$parameter->name], $path);
                            break;
                        case 'header':
                            $headers->set($parameter->name, $parameters[$parameter->name]);
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
                } elseif ($parameter->required && !$skipRequired) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Parameter "%s" is required, please pass value for this in $parameters',
                            $parameter->name
                        )
                    );
                }
            }
        }

        $uri = new \Slim\Http\Uri('http', '', null, $path);

        return new Request(
            $operation->method,
            $uri,
            $headers,
            [],
            [],
            $body
        );
    }
}
