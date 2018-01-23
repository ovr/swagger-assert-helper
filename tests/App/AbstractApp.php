<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\App;

use Api\Controller\UserController;

abstract class AbstractApp
{
    /**
     * @param $uri
     * @param $method
     * @return array
     */
    public function dispatch($uri, $method)
    {
        switch ($uri) {
            case '/v1/user/1':
                return (new UserController())->getAction();
            case '/v1/user':
                return (new UserController())->createAction();
            case '/v1/user/1/friends':
                return (new UserController())->getFriendsAction();
        }

        throw new \RuntimeException("Unsupported uri '{$uri}'");
    }
}