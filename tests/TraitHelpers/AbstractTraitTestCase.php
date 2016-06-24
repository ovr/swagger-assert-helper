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

    /**
     * @return array
     */
    public function getDataProviderForSimpleOperations()
    {
        return [
            [
                'createUser',
                '/v1/user',
                [
                    'id' => 1
                ]
            ],
            [
                'getUserById',
                '/v1/user/1',
                [
                    'id' => 1
                ]
            ],
            [
                'updateUserById',
                '/v1/user/1',
                [
                    'id' => 1
                ]
            ],
            [
                'deleteUserById',
                '/v1/user/1',
                [
                    'id' => 1
                ]
            ]
        ];
    }
}
