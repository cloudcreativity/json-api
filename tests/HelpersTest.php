<?php

namespace CloudCreativity\JsonApi;

use CloudCreativity\JsonApi\Exceptions\InvalidJsonException;

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
     * @param InvalidJsonException $ex
     */
    private function assertJsonError(InvalidJsonException $ex)
    {
        $this->assertEquals(json_last_error(), $ex->getJsonError());
        $this->assertEquals(json_last_error_msg(), $ex->getJsonErrorMessage());
    }
}
