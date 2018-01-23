<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\App;

use Slim\Http\Request;
use Slim\Http\Response;

class SlimApp extends AbstractApp
{
    public function handle(Request $request)
    {
        $response = new Response();

        $response->write(json_encode($this->dispatch($request->getUri(), $request->getMethod())));
        $response->withHeader('Content-Type', 'application/json');

        return $response;
    }
}