<?php

namespace CloudCreativity\JsonApi\Http\Middleware;

use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Http\HttpFactoryInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Psr\Http\Message\ServerRequestInterface;

trait NegotiatesContent
{

    /**
     * Perform content negotiation.
     *
     * @param HttpFactoryInterface $httpFactory
     * @param ServerRequestInterface $request
     * @param CodecMatcherInterface $codecMatcher
     * @throws JsonApiException
     * @see http://jsonapi.org/format/#content-negotiation
     */
    protected function doContentNegotiation(
        HttpFactoryInterface $httpFactory,
        ServerRequestInterface $request,
        CodecMatcherInterface $codecMatcher
    ) {
        $parser = $httpFactory->createHeaderParametersParser();
        $checker = $httpFactory->createHeadersChecker($codecMatcher);

        $checker->checkHeaders($parser->parse($request));
    }
}
