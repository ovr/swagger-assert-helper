<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\App;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SymfonyApp extends AbstractApp
{
    public function handle(Request $request)
    {
        $response = new JsonResponse();

        $response->setData($this->dispatch($request->getRequestUri(), $request->getMethod()));
        return $response;
    }
}