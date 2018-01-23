<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\App;

use Zend\Http\Request;
use Zend\Http\Response;

class ZendApp extends AbstractApp
{
    public function handle(Request $request)
    {
        $response = new Response();

        $response->setContent(json_encode($this->dispatch($request->getUri(), $request->getMethod())));
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');

        return $response;
    }
}