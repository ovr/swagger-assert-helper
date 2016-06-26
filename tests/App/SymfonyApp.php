<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\App;

use Api\Controller\UserController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SymfonyApp
{
    public function handle(Request $request)
    {
        $response = new JsonResponse();

        $controller = new UserController();
        $result = false;

        switch ($request->getRequestUri()) {
            case '/v1/user/1':
                $result = $controller->getAction();
                break;
            case '/v1/user/1/friends':
                $result = $controller->getFriendsAction();
                break;
        }

        $response->setData($result);
        return $response;
    }
}