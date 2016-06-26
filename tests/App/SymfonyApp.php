<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\App;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SymfonyApp
{
    public function handle(Request $request)
    {
        $response = new JsonResponse();

        $controller = new \UserController();
        $response->setData($controller->createAction());

        return $response;
    }
}