<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Ovr\Swagger;

class SwaggerWrapper
{
    /**
     * @var \Swagger\Annotations\Swagger
     */
    protected $swagger;

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
            if ($path->operationId = $operationId) {
                return $path;
            }
        }

        return null;
    }
}
