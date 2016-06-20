<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Ovr\Swagger;

use Swagger\Annotations\Operation;
use Symfony\Component\HttpFoundation\Request;

trait SymfonyTrait
{
    /**
     * Prepare a Symfony Request by Operation with $parameters
     *
     * @param Operation $operation
     * @param array $parameters
     * @return Request
     */
    public function makeRequestByOperation(Operation $operation, array $parameters = [])
    {
        $request = new Request();

        $path = $operation->path;

        if ($parameters) {
            foreach ($parameters as $key => $value) {
                $path = str_replace('{' . $key . '}', $value, $path);
            }
        }

        if ($operation->parameters) {
            foreach ($operation->parameters as $parameter) {
                if ($parameter->in == 'formData') {
                    $request->request->set($parameter->name, $parameters[$parameter->name]);
                }
            }
        }

        $request->server->set('REQUEST_URI', $path);
        $request->server->set('REQUEST_METHOD', $operation->method);

        return $request;
    }
}
