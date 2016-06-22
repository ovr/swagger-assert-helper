<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\TraitHelpers;

use Illuminate\Http\Request;

class LaravelTraitTest extends AbstractTraitTestCase
{
    use \Ovr\Swagger\LaravelTrait;

    public function testHelper()
    {
        $operation = $this->getSwaggerWrapper()->getOperationByName('getUserById');

        $request = $this->makeRequestByOperation($operation, ['id' => 1], false);
        parent::assertInstanceOf(Request::class, $request);
        parent::assertSame(Request::METHOD_GET, $request->getMethod());
        parent::assertSame('/v1/user/1', $request->getRequestUri());
    }
}
