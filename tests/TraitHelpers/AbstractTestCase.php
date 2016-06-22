<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\TraitHelpers;

abstract class AbstractTraitTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Ovr\Swagger\SwaggerWrapper
     */
    protected function getSwaggerWrapper()
    {
        return new \Ovr\Swagger\SwaggerWrapper(
            \Swagger\scan(
                __DIR__ . '/../../examples'
            )
        );
    }
}
