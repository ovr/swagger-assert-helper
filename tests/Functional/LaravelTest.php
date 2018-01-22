<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\Functional;

use Ovr\Swagger\LaravelTrait;
use Tests\App\LaravelApp;

class LaravelTest extends AbstractTestCase
{
    use LaravelTrait;

    /**
     * @return LaravelApp
     */
    protected function getApp()
    {
        return new LaravelApp();
    }
}