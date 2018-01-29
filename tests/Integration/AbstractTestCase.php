<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\Integration;

use Ovr\Swagger\SwaggerWrapper;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method RequestInterface makeRequestByOperation();
 */
abstract class AbstractTestCase extends \PHPUnit\Framework\TestCase
{
    public function testGetUserById()
    {
        $operation = $this->getSwaggerWrapper()->getOperationByName('getUserById');
        $response = $this->makeRequest(
            $this->getSwaggerWrapper(),
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

    abstract protected function makeRequest(SwaggerWrapper $swaggerWrapper, RequestInterface $request);

    /**
     * @return \Ovr\Swagger\SwaggerWrapper
     */
    protected function getSwaggerWrapper()
    {
        return new \Ovr\Swagger\SwaggerWrapper(
            \Swagger\scan(
                __DIR__ . '/../../examples/github'
            )
        );
    }
}