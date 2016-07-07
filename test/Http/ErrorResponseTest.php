<?php

namespace CloudCreativity\JsonApi\Http;

use CloudCreativity\JsonApi\Document\Error;
use CloudCreativity\JsonApi\Exceptions\MutableErrorCollection;
use CloudCreativity\JsonApi\TestCase;
use Neomerx\JsonApi\Exceptions\JsonApiException;

final class ErrorResponseTest extends TestCase
{

    public function testResolveErrorStatusNoStatus()
    {
        $response = new ErrorResponse(new Error());
        $this->assertEquals(JsonApiException::DEFAULT_HTTP_CODE, $response->getHttpCode());
    }

    public function testResolveErrorStatusUsesDefaultWithMultiple()
    {
        $response = new ErrorResponse([new Error(), new Error()], 499);
        $this->assertEquals(499, $response->getHttpCode());
    }

    public function testResolveErrorStatusUsesErrorStatus()
    {
        $response = new ErrorResponse([new Error(), new Error(null, null, 422)]);
        $this->assertEquals(422, $response->getHttpCode());
    }

    public function testResolveErrorStatus4xx()
    {
        $response = new ErrorResponse([new Error(null, null, 422), new Error(null, null, 415)]);
        $this->assertEquals(400, $response->getHttpCode());
    }

    public function testResolveErrorStatus5xx()
    {
        $response = new ErrorResponse([new Error(null, null, 501), new Error(null, null, 503)]);
        $this->assertEquals(500, $response->getHttpCode());
    }

    public function testResolveErrorStatusMixed()
    {
        $a = new Error(null, null, 422);
        $b = new Error(null, null, 501);
        $response = new ErrorResponse([$a, $b]);

        $this->assertEquals(500, $response->getHttpCode());
        $this->assertSame([$a, $b], $response->getErrors());
    }

    public function testHeaders()
    {
        $headers = ['X-Custom' => 'Foobar'];
        $response = new ErrorResponse([], null, $headers);
        $this->assertEquals($headers, $response->getHeaders());
    }

    public function testCreate()
    {
        $error = new Error('123', null, 422);
        $ex = new JsonApiException([$error], 403);
        $headers = ['X-Custom' => 'Foobar'];
        $response = ErrorResponse::create($ex, $headers);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        /** @var ErrorResponse $response */
        $this->assertSame($ex->getErrors(), $response->getErrors());
        $this->assertEquals($ex->getHttpCode(), $response->getHttpCode());
    }
}
