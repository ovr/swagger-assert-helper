<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests;

use Flow\JSONPath\JSONPath;
use Ovr\Swagger\SwaggerWrapper;
use Swagger\Annotations\Definition;
use Swagger\Annotations\Property;

/**
 * It's needed to access protected methods
 */
class SwaggerWrapperMock extends SwaggerWrapper
{
    /**
     * {@inheritdoc}
     */
    public function validateScheme(Definition $scheme, JSONPath $jsonPath)
    {
        return parent::validateScheme($scheme, $jsonPath);
    }

    /**
     * {@inheritdoc}
     */
    public function validateProperty(Property $property, $value)
    {
        return parent::validateProperty($property, $value);
    }
}
