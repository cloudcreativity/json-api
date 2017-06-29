<?php

namespace CloudCreativity\JsonApi\Http\Client;

use Psr\Http\Message\ResponseInterface;

class Response
{

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * Response constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return ResponseInterface
     */
    public function getPsrResponse()
    {
        return $this->response;
    }
}
