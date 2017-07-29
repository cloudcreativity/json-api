<?php

namespace CloudCreativity\JsonApi;

use CloudCreativity\JsonApi\Exceptions\InvalidJsonException;
use GuzzleHttp\Psr7\ServerRequest;

class HelpersTest extends TestCase
{

    /**
     * @return array
     */
    public function invalidJsonProvider()
    {
        return [
            'parse error' => ['{ "data": { "type": "foo" }', true],
            'empty string' => [''],
            'null' => ['NULL'],
            'integer' => ['1'],
            'bool' => ['true'],
            'string' => ['foo'],
        ];
    }

    /**
     * @param $content
     * @param bool $jsonError
     * @dataProvider invalidJsonProvider
     */
    public function testInvalidJson($content, $jsonError = false)
    {
        try {
            json_decode($content);
            $this->fail('No exception thrown.');
        } catch (InvalidJsonException $ex) {
            if ($jsonError) {
                $this->assertJsonError($ex);
            }
        }
    }

    /**
     * @return array
     */
    public function requestContainsBodyProvider()
    {
        return [
            'neither header' => [[], false],
            'content-length' => [['Content-Length' => '120'], true],
            'zero content-length' => [['Content-Length' => '0'], false],
            'empty content-length' => [['Content-Length' => ''], false],
            'transfer-encoding 1' => [['Transfer-Encoding' => 'chunked'], true],
            'transfer-encoding 2' => [['Transfer-Encoding' => 'gzip, chunked'], true],
            'content-type no content-length' => [['Content-Type' => 'text/plain'], false],
        ];
    }

    /**
     * @param array $headers
     * @param $expected
     * @dataProvider requestContainsBodyProvider
     */
    public function testHttpContainsBody(array $headers, $expected)
    {
        $request = new ServerRequest('GET', '/api/posts', $headers);

        $this->assertSame($expected, http_contains_body($request));
    }

    /**
     * @param InvalidJsonException $ex
     */
    private function assertJsonError(InvalidJsonException $ex)
    {
        $this->assertEquals(json_last_error(), $ex->getJsonError());
        $this->assertEquals(json_last_error_msg(), $ex->getJsonErrorMessage());
    }
}
