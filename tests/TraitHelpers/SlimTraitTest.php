<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\TraitHelpers;

use Slim\Http\Request;

class SlimTraitTest extends AbstractTraitTestCase
{
    use \Ovr\Swagger\SlimTrait;

    public function testHelper()
    {
        $operation = $this->getSwaggerWrapper()->getOperationByName('getUserById');

        $request = $this->makeRequestByOperation($operation, ['id' => 1], false);
        parent::assertInstanceOf(Request::class, $request);
        parent::assertSame('GET', $request->getMethod());
        parent::assertSame('/v1/user/1', $request->getUri()->getPath());
    }
}
