<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\TraitHelpers;

use Slim\Http\Request;

class SlimTraitTest extends AbstractTraitTestCase
{
    use \Ovr\Swagger\SlimTrait;

    /**
     * @dataProvider getDataProviderForSimpleOperations
     *
     * @param string $operationId
     * @param string $url
     * @param array $parameters
     */
    public function testMakeRequestByOperationSimpleSuccess($operationId, $url, array $parameters)
    {
        if ($operationId === 'createUser') {
            $this->markTestSkipped('formData not supported, @todo!');
        }

        $operation = $this->getSwaggerWrapper()->getOperationByName($operationId);

        $request = $this->makeRequestByOperation($operation, $parameters, false);
        parent::assertInstanceOf(Request::class, $request);
        parent::assertSame(strtoupper($operation->method), $request->getMethod());
        parent::assertSame($url, $request->getUri()->getPath());
    }
}
