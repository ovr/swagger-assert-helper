<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\TraitHelpers;

abstract class AbstractTestCase extends \PHPUnit\Framework\TestCase
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
                    'username' => 'ovr',
                    'X-AUTH-TOKEN' => 'MEOW'
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
                    'id' => 1,
                    'X-AUTH-TOKEN' => 'MEOW'
                ]
            ],
            [
                'deleteUserById',
                '/v1/user/1',
                [
                    'id' => 1,
                    'X-AUTH-TOKEN' => 'MEOW'
                ]
            ],
            [
                'getUserFriendsById',
                '/v1/user/1/friends',
                [
                    'id' => 1
                ]
            ]
        ];
    }
}
