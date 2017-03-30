<?php

namespace CloudCreativity\JsonApi\Contracts\Factories;

use CloudCreativity\JsonApi\Contracts\Http\ApiInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use Neomerx\JsonApi\Contracts\Factories\FactoryInterface as BaseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

interface FactoryInterface extends BaseFactoryInterface
{

    /**
     * Build a JSON API request object from an API definition and an HTTP request.
     *
     * @param ServerRequestInterface $httpRequest
     *      the inbound HTTP request
     * @param RequestInterpreterInterface $interpreter
     *      the interpreter to analyze the request
     * @param ApiInterface $api
     *      the API that is receiving the request
     * @return RequestInterface
     */
    public function createRequest(
        ServerRequestInterface $httpRequest,
        RequestInterpreterInterface $interpreter,
        ApiInterface $api
    );
}
