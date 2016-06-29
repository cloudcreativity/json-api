<?php

namespace CloudCreativity\JsonApi\Exceptions;

use Exception;
use Neomerx\JsonApi\Exceptions\JsonApiException;

class InvalidJsonException extends JsonApiException
{

    /**
     * @var int
     */
    private $jsonError;

    /**
     * @var string
     */
    private $jsonErrorMessage;

    /**
     * @param int $defaultHttpCode
     * @param Exception|null $previous
     * @return InvalidJsonException
     */
    public static function create($defaultHttpCode = self::HTTP_CODE_BAD_REQUEST, Exception $previous = null)
    {
        return new self(json_last_error(), json_last_error_msg(), $defaultHttpCode, $previous);
    }

    /**
     * InvalidJsonException constructor.
     * @param int $jsonError
     * @param string $jsonErrorMessage
     * @param int $defaultHttpCode
     * @param Exception|null $previous
     */
    public function __construct(
        $jsonError,
        $jsonErrorMessage,
        $defaultHttpCode = self::HTTP_CODE_BAD_REQUEST,
        Exception $previous = null
    ) {
        parent::__construct([], $defaultHttpCode, $previous);
        $this->jsonError = $jsonError;
        $this->jsonErrorMessage = $jsonErrorMessage;
    }

    /**
     * @return string
     */
    public function getJsonError()
    {
        return $this->jsonError;
    }

    public function getJsonErrorMessage()
    {
        return $this->jsonErrorMessage;
    }
}
