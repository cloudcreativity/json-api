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

namespace CloudCreativity\JsonApi\Exceptions;

use DateTime;
use Neomerx\JsonApi\Contracts\Integration\NativeResponsesInterface;
use Neomerx\JsonApi\Parameters\Headers\MediaType;
use Neomerx\JsonApi\Responses\Responses;

class RendererTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Responses
     */
    protected $responses;

    /**
     * @var MediaType
     */
    protected $mediaType;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $nativeResponses;

    private $response;

    protected function setUp()
    {
        /** @var NativeResponsesInterface $nativeResponses */
        $nativeResponses = $this->getMock(NativeResponsesInterface::class);

        $this->responses = new Responses($nativeResponses);
        $this->nativeResponses = $nativeResponses;
        $this->response = new DateTime();
        $this->mediaType = MediaType::parse(0, MediaType::JSON_API_MEDIA_TYPE);
    }

    /**
     * @param $content
     * @param $status
     */
    protected function expectedResponse($content, $status)
    {
        $headers = [
            Responses::HEADER_CONTENT_TYPE => $this->mediaType->getMediaType(),
        ];

        $this->nativeResponses
            ->method('createResponse')
            ->with($content, $status, $headers)
            ->willReturn($this->response);
    }

    /**
     * @param $actual
     */
    protected function assertResponse($actual)
    {
        $this->assertSame($this->response, $actual, 'Did not receive the expected response.');
    }
}
