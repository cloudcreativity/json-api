<?php

namespace CloudCreativity\JsonApi\Contracts\Factories;

use CloudCreativity\JsonApi\Contracts\Http\ApiInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterface;
use Neomerx\JsonApi\Contracts\Factories\FactoryInterface as BaseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

interface FactoryInterface extends BaseFactoryInterface
{

    /**
     * Build a JSON API request object from an API definition and an HTTP request.
     *
     * @param ApiInterface $api
     * @param ServerRequestInterface $httpRequest
     * @return RequestInterface
     */
    public function createRequest(ApiInterface $api, ServerRequestInterface $httpRequest);
}
