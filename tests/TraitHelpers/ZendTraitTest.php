<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\TraitHelpers;

use Zend\Http\Request;

class ZendTraitTest extends AbstractTraitTestCase
{
    use \Ovr\Swagger\ZendTrait;

    public function testHelper()
    {
        $operation = $this->getSwaggerWrapper()->getOperationByName('getUserById');

        $request = $this->makeRequestByOperation($operation, ['id' => 1], false);
        parent::assertInstanceOf(Request::class, $request);
        parent::assertSame(Request::METHOD_GET, $request->getMethod());
        parent::assertSame('/v1/user/1', $request->getUriString());
    }
}
