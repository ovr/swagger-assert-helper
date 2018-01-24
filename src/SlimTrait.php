<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Ovr\Swagger;

use InvalidArgumentException;
use RuntimeException;
use Slim\Http\Request;
use Slim\Http\Response;
use Swagger\Annotations\Operation;

trait SlimTrait
{
    /**
     * Prepare a Slim Request by Operation with $parameters
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
        $headers = new \Slim\Http\Headers();
        $body = new \Slim\Http\RequestBody();

        $path = $operation->path;

        if ($operation->parameters) {
            foreach ($operation->parameters as $parameter) {
                if (array_key_exists($parameter->name, $parameters)) {
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

    /**
     * @param Response $response
     * @return ResponseData
     */
    protected function extractResponseData(Response $response)
    {
        return ResponseData::factory(
            $response->getHeader('content-type'),
            (string) $response->getBody(),
            $response->getStatusCode()
        );
    }
}
