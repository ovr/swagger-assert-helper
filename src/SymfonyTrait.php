<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Ovr\Swagger;

use RuntimeException;
use Swagger\Annotations\Operation;
use Swagger\Annotations\Parameter;
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

        SwaggerWrapper::hydrateRequestByOperation(
            $operation,
            function (Parameter $parameter, $value) use ($request, &$path) {
                switch ($parameter->in) {
                    case 'path':
                        $path = str_replace('{' . $parameter->name . '}', $value, $path);
                        break;
                    case 'header':
                        $request->headers->set($parameter->name, $value);
                        break;
                    case 'query':
                        $request->query->set($parameter->name, $value);
                        break;
                    case 'formData':
                        $request->request->set($parameter->name, $value);
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
        return ResponseData::factory(
            $response->headers->get('content-type'),
            $response->getContent(),
            $response->getStatusCode()
        );
    }
}
