<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\TraitHelpers;

use Symfony\Component\HttpFoundation\Request;

class SymfonyTraitTest extends AbstractTraitTestCase
{
    use \Ovr\Swagger\SymfonyTrait;

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
