<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\TraitHelpers;

use Illuminate\Http\Request;

class LaravelTest extends AbstractTestCase
{
    use \Ovr\Swagger\LaravelTrait;

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
        parent::assertSame($url, $request->getRequestUri());
    }
}
