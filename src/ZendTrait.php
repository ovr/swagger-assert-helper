<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Ovr\Swagger;

use InvalidArgumentException;
use RuntimeException;
use Swagger\Annotations\Operation;
use Swagger\Annotations\Parameter;
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

        SwaggerWrapper::hydrateRequestByOperation(
            $operation,
            function (Parameter $parameter, $value) use ($request, &$path) {
                switch ($parameter->in) {
                    case 'header':
                        $request->getHeaders()->addHeaderLine($parameter->name, $value);
                        break;
                    case 'path':
                        $path = str_replace('{' . $parameter->name . '}', $value, $path);
                        break;
                    case 'query':
                        $request->getQuery()->set($parameter->name, $value);
                        break;
                    case 'formData':
                        $request->getPost()->set($parameter->name, $value);
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

            return ResponseData::factory(
                $contentType,
                (string) $response->getContent(),
                $response->getStatusCode()
            );
        }

        throw new RuntimeException('Unknown HTTP "content-type"');
    }
}
