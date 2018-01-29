<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

declare(strict_types=1);

namespace Ovr\Swagger;

use RuntimeException;

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

    /**
     * @param string $contentType
     * @param string $content
     * @param int $statusCode
     * @return ResponseData
     */
    static public function factory(string $contentType, string $content, int $statusCode)
    {
        switch ($contentType) {
            case 'application/json':
            case 'application/json; charset=utf-8':
                return new ResponseData(
                    $content,
                    $statusCode
                );
            default:
                throw new RuntimeException("HTTP content-type: {$contentType} does not supported");
        }
    }
}
