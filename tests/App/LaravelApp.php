<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\App;

use Api\Controller\UserController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LaravelApp
{
    public function handle(Request $request)
    {
        $response = new Response();

        $controller = new UserController();
        $result = false;

        switch ($request->getRequestUri()) {
            case '/v1/user/1':
                $result = $controller->getAction();
                break;
            case '/v1/user':
                $result = $controller->createAction();
                break;
            case '/v1/user/1/friends':
                $result = $controller->getFriendsAction();
                break;
        }

        $response->setContent(json_encode($result));
        $response->header('Content-Type', 'application/json');

        return $response;
    }
}