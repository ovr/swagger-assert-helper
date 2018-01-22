<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\Functional;

use Ovr\Swagger\ZendTrait;
use Tests\App\ZendApp;

class ZendTest extends AbstractTestCase
{
    use ZendTrait;

    /**
     * @return ZendApp
     */
    protected function getApp()
    {
        return new ZendApp();
    }
}