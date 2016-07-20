<?php

/**
 * Copyright 2016 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Testing;

use Neomerx\JsonApi\Contracts\Document\DocumentInterface as Keys;
use PHPUnit_Framework_Assert as PHPUnit;
use stdClass;

/**
 * Class DocumentTester
 * @package CloudCreativity\JsonApi
 */
class DocumentTester
{

    /**
     * @var stdClass
     */
    private $document;

    /**
     * Create a document tester from a raw HTTP response content.
     *
     * @param string $responseContent
     * @return DocumentTester
     */
    public static function create($responseContent)
    {
        $decoded = json_decode($responseContent);

        if (JSON_ERROR_NONE !== json_last_error()) {
            PHPUnit::fail('Invalid response JSON: ' . json_last_error_msg());
        }

        if (!is_object($decoded)) {
            PHPUnit::fail('Invalid JSON API response content.');
        }

        return new self($decoded);
    }

    /**
     * DocumentTester constructor.
     * @param stdClass $document
     */
    public function __construct(stdClass $document)
    {
        $this->document = $document;
    }

    /**
     * Assert that the document has an errors key, and return an errors tester.
     *
     * @param string|null $message
     * @return ErrorsTester
     */
    public function assertErrors($message = null)
    {
        $message = $message ?: 'Document does not contain errors.';
        PHPUnit::assertObjectHasAttribute(Keys::KEYWORD_ERRORS, $this->document, $message);

        return new ErrorsTester((array) $this->document->{Keys::KEYWORD_ERRORS});
    }
}
