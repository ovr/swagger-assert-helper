<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Tests\App\SymfonyApp;

abstract class AbstractTestCase extends \PHPUnit\Framework\TestCase
{
    public function testGetUserById()
    {
        $operation = $this->getSwaggerWrapper()->getOperationByName('getUserById');
        $response = $this->getApp()->handle(
            $this->makeRequestByOperation(
                $operation,
                [
                    'id' => 1
                ]
            )
        );
        $this->getSwaggerWrapper()
            ->assertHttpResponseForOperation($this->extractResponseData($response), $operation, Response::HTTP_OK);
    }

    public function testCreateUser()
    {
        $operation = $this->getSwaggerWrapper()->getOperationByName('createUser');
        $response = $this->getApp()->handle(
            $this->makeRequestByOperation(
                $operation,
                [
                    'username' => 'ovr',
                    'X-AUTH-TOKEN' => 'MEOW'
                ]
            )
        );
        $this->getSwaggerWrapper()
            ->assertHttpResponseForOperation($this->extractResponseData($response), $operation, Response::HTTP_OK);
    }

    public function testGetUserFriendsById()
    {
        $operation = $this->getSwaggerWrapper()->getOperationByName('getUserFriendsById');
        $response = $this->getApp()->handle(
            $this->makeRequestByOperation(
                $operation,
                [
                    'id' => 1
                ]
            )
        );
        $this->getSwaggerWrapper()
            ->assertHttpResponseForOperation($this->extractResponseData($response), $operation, Response::HTTP_OK);
    }

    abstract protected function getApp();

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