<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\Integration;

use GuzzleHttp\Client;
use Ovr\Swagger\GuzzleTrait;
use Ovr\Swagger\SwaggerWrapper;
use Psr\Http\Message\RequestInterface;

class GuzzleTest extends AbstractTestCase
{
    use GuzzleTrait;

    protected function makeRequest(SwaggerWrapper $swaggerWrapper, RequestInterface $request)
    {
        $request = $request->withUri(
            $request
                ->getUri()
                ->withHost(
                    $swaggerWrapper->getHost()
                )->withScheme(
                    $swaggerWrapper->getScheme()
                )
        );

        $client = new Client();
        return $client->send($request);
    }
}
