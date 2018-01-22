<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

declare(strict_types=1);

namespace Ovr\Swagger;

class ResponseData
{
    /**
     * @var mixed
     */
    protected $content;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @param $content
     * @param int $statusCode
     */
    public function __construct($content, int $statusCode)
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
