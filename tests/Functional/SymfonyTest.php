<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace Tests\Functional;

use Ovr\Swagger\SymfonyTrait;
use Tests\App\SymfonyApp;

class SymfonyTest extends AbstractTestCase
{
    use SymfonyTrait;

    /**
     * @return SymfonyApp
     */
    protected function getApp()
    {
        return new SymfonyApp();
    }
}