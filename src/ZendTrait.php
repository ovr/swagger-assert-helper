<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Ovr\Swagger;

use InvalidArgumentException;
use RuntimeException;
use Swagger\Annotations\Operation;
use Zend\Http\Request;
use Zend\Http\Response;

trait ZendTrait
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
        $request = new Request();

        $path = $operation->path;

        if ($operation->parameters) {
            foreach ($operation->parameters as $parameter) {
                if (isset($parameters[$parameter->name])) {
                    switch ($parameter->in) {
                        case 'header':
                            $request->getHeaders()->addHeaderLine($parameter->name, $parameters[$parameter->name]);
                            break;
                        case 'path':
                            $path = str_replace('{' . $parameter->name . '}', $parameters[$parameter->name], $path);
                            break;
                        case 'query':
                            $request->getQuery()->set($parameter->name, $parameters[$parameter->name]);
                            break;
                        case 'formData':
                            $request->getPost()->set($parameter->name, $parameters[$parameter->name]);
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

        $request->setUri($path);
        $request->setMethod($operation->method);

        return $request;
    }

    /**
     * @param Response $response
     * @return ResponseData
     */
    protected function extractResponseData(Response $response)
    {
        $header = $response->getHeaders()->get('content-type');
        if ($header) {
            $contentType = $header->getFieldValue();
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

        throw new RuntimeException('Unknown content-type');
    }
}
