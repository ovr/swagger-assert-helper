<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\App;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LaravelApp extends AbstractApp
{
    public function handle(Request $request)
    {
        $response = new Response();

        $response->setContent(json_encode($this->dispatch($request->getRequestUri(), $request->getMethod())));
        $response->header('Content-Type', 'application/json');

        return $response;
    }
}