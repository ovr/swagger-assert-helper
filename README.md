Swagger Assert Helper
=====================
[![Build Status](https://travis-ci.org/ovr/swagger-assert-helper.svg?branch=master)](https://travis-ci.org/ovr/swagger-assert-helper)

This library bring support for:
 
- Making HTTP Request by Swagger Path
- Asserting HTTP Response with Swagger Response Scheme
- Functional testing on top of frameworks: `Zend`, `Laravel`, `Slim`, `Symfony`
- Integration testing on top of: `Guzzle`

## Installing via Composer

You can use [Composer](https://getcomposer.org) .

```bash
composer require ovr/swagger-assert-helper
```

# How to use?

# 1. Write Swagger Definition for your API:

I love to use PHP comments, example:

```php
/**
 * @SWG\Definition(
 *  definition = "UserResponse",
 *  required={"id", "name"},
 *  @SWG\Property(property="id", type="integer", format="int64"),
 *  @SWG\Property(property="name", type="string"),
 * );
 */
class UserController extends AbstractController
{
    /**
     * @SWG\Get(
     *  tags={"User"},
     *  path="/user/{id}",
     *  operationId="getUserById",
     *  summary="Find user by $id",
     *  @SWG\Parameter(
     *      name="id",
     *      description="$id of the specified",
     *      in="path",
     *      required=true,
     *      type="string"
     *  ),
     *  @SWG\Response(
     *      response=200,
     *      description="success",
     *      @SWG\Schema(ref="#/definitions/UserResponse")
     *  ),
     *  @SWG\Response(
     *      response=404,
     *      description="Not found"
     *  )
     * )
     */
    public function getAction() {}
}
```

More definition examples you can find in:

- [Example/API](examples/api) - example of small HTTP REST service
- [Example/GitHub](examples/github) - example definitions for `api.github.com`

# 2. Write test for your Controller

## Functional

Functional - when you execute `Request` inside your service (`PHP` code), there are support for:

- Symfony by [SymfonyTrait](src/SymfonyTrait.php)
- Laravel by [LaravelTrait](src/LaravelTrait.php)
- Zend by [ZendTrait](src/ZendTrait.php)
- Slim by [SlimTrait](src/SlimTrait)

Example:

```php
class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    // You should use trait for your framework, review supported and use what you need
    use \Ovr\Swagger\SymfonyTrait;
    
    public function testGetUserById()
    {
        // We define operation called getUserById in first step!
        $operation = $this->getSwaggerWrapper()->getOperationByName('getUserById');
        
        // Call makeRequestByOperation from our framework Trait, SymfonyTrait for us
        $request = $this->makeRequestByOperation(
            $operation,
            [
                'id' => 1
            ]
        );
        
        // This will be \Symfony\Component\HttpFoundation\Request
        var_dump($request);
        
        // You should execute your API module by Request and get Response
        $response = $this->getApi()->handle($request);
        
        $this->getSwaggerWrapper()->assertHttpResponseForOperation(
            // Call makeRequestByOperation from our framework Trait, SymfonyTrait for us
            $this->extractResponseData($response),
            // getUserById
            $operation,
            // Operation can response by codes that your defined, lets assert that it will be 200 (HTTP_OK)
            Response::HTTP_OK
        );
    }
    
    /**
     * Return API module/service/bundle, that handle request and return Response for it
     */
    abstract public function getApi();
}
```

## Integration

Integration - when you execute `Request` by real transport, there are support for:

- Guzzle by [GuzzleTrait](src/GuzzleTrait.php)

# FAQ

<dl>
  <dt>Q: Can this library validate my Swagger definition?</dt>
  <dd>A: No. This library validate your API requests and responses match your Swagger definition.</dd>
</dl>

<dl>
  <dt>Q: What content types are supported?</dt>
  <dd>A: JSON for now, ask me for XML or something else in the issues.</dd>
</dl>

# LICENSE

This project is open-sourced software licensed under the MIT License.

See the [LICENSE](LICENSE) file for more information.
