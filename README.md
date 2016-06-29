Swagger Assert Helper
=====================
[![Build Status](https://travis-ci.org/ovr/swagger-assert-helper.svg?branch=master)](https://travis-ci.org/ovr/swagger-assert-helper)

This library bring support for:
 
- Asserting HTTP Response with Swagger Response Scheme
- Making HTTP Request by Swagger Path

# How to use?

### Making HTTP Request

```php
class RequestTest {
    // You should use trait for your framework, review supported and use what you need
    use \Ovr\Swagger\SymfonyTrait;
    
    public function testGetUserById()
    {
        $operation = $this->getSwaggerWrapper()->getOperationByName('getUserById');
        $request = $this->makeRequestByOperation(
            $operation,
            [
                'id' => 1
            ]
        );
        var_dump($request); // This will be \Symfony\Component\HttpFoundation\Request
    }
}
```

# LICENSE

MIT
