<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\TraitHelpers;

use Slim\Http\Request;

class SlimTraitTest extends \PHPUnit_Framework_TestCase
{
    use \Ovr\Swagger\SlimTrait;

    protected function getSwaggerWrapper()
    {
        return new \Ovr\Swagger\SwaggerWrapper(
            \Swagger\scan(
                __DIR__ . '/../../examples'
            )
        );
    }

    public function testHelper()
    {
        $operation = $this->getSwaggerWrapper()->getOperationByName('getUserById');

        $request = $this->makeRequestByOperation($operation, ['id' => 1], false);
        parent::assertInstanceOf(Request::class, $request);
        parent::assertSame('GET', $request->getMethod());
        parent::assertSame('/v1/user/1', $request->getUri()->getPath());
    }
}
