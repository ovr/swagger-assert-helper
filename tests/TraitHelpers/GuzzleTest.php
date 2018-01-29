<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\TraitHelpers;

use GuzzleHttp\Psr7\Request;

class GuzzleTest extends AbstractTestCase
{
    use \Ovr\Swagger\GuzzleTrait;

    /**
     * @dataProvider getDataProviderForSimpleOperations
     *
     * @param string $operationId
     * @param string $url
     * @param array $parameters
     */
    public function testMakeRequestByOperationSimpleSuccess($operationId, $url, array $parameters)
    {
        $operation = $this->getSwaggerWrapper()->getOperationByName($operationId);

        $request = $this->makeRequestByOperation($operation, $parameters, false);
        parent::assertInstanceOf(Request::class, $request);
        parent::assertSame(strtoupper($operation->method), $request->getMethod());
        parent::assertSame($url, $request->getUri()->getPath());
    }
}
