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

use CloudCreativity\JsonApi\Error\ErrorObject;
use InvalidArgumentException;
use Neomerx\JsonApi\Encoder\Encoder;
use RuntimeException;

class StandardRendererTest extends RendererTestCase
{

    /**
     * @var Encoder
     */
    private $encoder;

    /**
     * @var StandardRenderer
     */
    private $renderer;

    protected function setUp()
    {
        parent::setUp();

        $this->renderer = new StandardRenderer($this->responses);
        $this->encoder = Encoder::instance([]);

        $this->renderer->withMediaType($this->mediaType);
        $this->renderer->setEncoder($this->encoder);
    }

    public function testDefaultStatus()
    {
        $this->renderer->configure([
            StandardRenderer::DEFAULT_STATUS => 501,
        ]);

        $this->expectedResponse(null, 501);
        $this->assertResponse($this->renderer->render(new RuntimeException()));
    }

    public function testExceptionStatus()
    {
        $this->renderer->configure([
            StandardRenderer::MAP => [
                RuntimeException::class => 418,
                InvalidArgumentException::class => 501,
            ],
        ]);

        $this->expectedResponse(null, 418);
        $this->assertResponse($this->renderer->render(new RuntimeException()));
    }

    public function testTemplate()
    {
        $error = new ErrorObject([
            ErrorObject::TITLE => 'My Error',
            ErrorObject::STATUS => 418,
        ]);

        $this->renderer->configure([
            StandardRenderer::MAP => [
                RuntimeException::class => $error->toArray(),
                InvalidArgumentException::class => [
                    ErrorObject::TITLE => 'Unexpected error.'
                ],
            ],
        ]);

        $content = $this->encoder->encodeError($error);

        $this->expectedResponse($content, 418);
        $this->assertResponse($this->renderer->render(new RuntimeException()));
    }
}
