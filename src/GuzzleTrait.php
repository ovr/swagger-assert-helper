<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Ovr\Swagger;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use RuntimeException;
use Swagger\Annotations\Operation;
use Swagger\Annotations\Parameter;

trait GuzzleTrait
{
    /**
     * Prepare a Zend Request by Operation with $parameters
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
        $path = $operation->path;

        $query = [];
        $body = [];
        $headers = [];

        SwaggerWrapper::hydrateRequestByOperation(
            $operation,
            function (Parameter $parameter, $value) use (&$headers, &$path) {
                switch ($parameter->in) {
                    case 'header':
                        $headers[$parameter->name] = $value;
                        break;
                    case 'path':
                        $path = str_replace('{' . $parameter->name . '}', $value, $path);
                        break;
                    case 'query':
                        $query[$parameter->name] = $value;
                        break;
                    case 'formData':
                        $body[$parameter->name] = $value;
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
            },
            $parameters,
            $options
        );

        return new Request(
            $operation->method,
            $path,
            $headers,
            json_encode($body)
        );
    }

    /**
     * @param Response $response
     * @return ResponseData
     */
    protected function extractResponseData(Response $response)
    {
        if (!$response->hasHeader('content-type')) {
            throw new RuntimeException('Unknown HTTP "content-type"');
        }

        return ResponseData::factory(
            current($response->getHeader('content-type')),
            (string) $response->getBody(),
            $response->getStatusCode()
        );
    }
}
