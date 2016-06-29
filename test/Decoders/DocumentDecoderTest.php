<?php

/**
 * Copyright 2015 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CloudCreativity\JsonApi\Decoders;

use CloudCreativity\JsonApi\Exceptions\InvalidJsonException;
use CloudCreativity\JsonApi\TestCase;
use Exception;

/**
 * Class DocumentDecoderTest
 * @package CloudCreativity\JsonApi
 */
class DocumentDecoderTest extends TestCase
{

    public function testInvalidJson()
    {
        $content = <<<JSON_API
        {
            "data": {
                "type": "foo"
        }
JSON_API;

        $decoder = new DocumentDecoder();

        try {
            $decoder->decode($content);
            $this->fail('No exception thrown.');
        } catch (Exception $ex) {
            $this->assertInstanceOf(InvalidJsonException::class, $ex);
            /** @var InvalidJsonException $ex */
            $this->assertEquals(json_last_error(), $ex->getJsonError());
            $this->assertEquals(json_last_error_msg(), $ex->getJsonErrorMessage());
        }
    }
}
