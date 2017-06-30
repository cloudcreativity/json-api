<?php

/**
 * Copyright 2017 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Http\Client;

use CloudCreativity\JsonApi\Contracts\Object\DocumentInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Response
 *
 * @package CloudCreativity\JsonApi
 */
class Response
{

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var DocumentInterface|null
     */
    private $document;

    /**
     * Response constructor.
     *
     * @param ResponseInterface $response
     * @param DocumentInterface $document
     */
    public function __construct(ResponseInterface $response, DocumentInterface $document = null)
    {
        $this->response = $response;
        $this->document = $document;
    }

    /**
     * @return ResponseInterface
     */
    public function getPsrResponse()
    {
        return $this->response;
    }

    /**
     * The parsed response document, if it had one.
     *
     * @return DocumentInterface|null
     */
    public function getDocument()
    {
        return $this->document;
    }
}
