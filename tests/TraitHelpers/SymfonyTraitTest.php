<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\TraitHelpers;

use Symfony\Component\HttpFoundation\Request;

class SymfonyTraitTest extends AbstractTraitTestCase
{
    use \Ovr\Swagger\SymfonyTrait;

    public function testHelper()
    {
        $operation = $this->getSwaggerWrapper()->getOperationByName('getUserById');

        $request = $this->makeRequestByOperation($operation, ['id' => 1], false);
        parent::assertInstanceOf(Request::class, $request);
        parent::assertSame(Request::METHOD_GET, $request->getMethod());
        parent::assertSame('/v1/user/1', $request->getRequestUri());
    }
}
