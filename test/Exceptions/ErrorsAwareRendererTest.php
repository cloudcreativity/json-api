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

use CloudCreativity\JsonApi\Contracts\Error\ErrorObjectInterface;
use CloudCreativity\JsonApi\Error\ErrorCollection;
use CloudCreativity\JsonApi\Error\MultiErrorException;
use Exception;
use Neomerx\JsonApi\Encoder\Encoder;

class ErrorsAwareRendererTest extends RendererTestCase
{

    const STATUS = 418;

    /**
     * @var ErrorsAwareRenderer
     */
    private $renderer;

    /**
     * @var Encoder
     */
    private $encoder;

    /**
     * @var MultiErrorException
     */
    private $exception;

    protected function setUp()
    {
        parent::setUp();

        $errors = new ErrorCollection();
        $errors->error([
            ErrorObjectInterface::TITLE => 'An error',
            ErrorObjectInterface::STATUS => self::STATUS,
        ]);

        $this->renderer = new ErrorsAwareRenderer($this->responses);
        $this->exception = new MultiErrorException($errors);
        $this->encoder = Encoder::instance([]);

        $this->renderer->setEncoder($this->encoder);
        $this->renderer->withMediaType($this->mediaType);
    }

    public function testRender()
    {
        $content = $this->encoder->encodeErrors($this->exception->getErrors()->getAll());

        $this->expectedResponse($content, self::STATUS);
        $this->assertResponse($this->renderer->render($this->exception));
    }

    /**
     * @depends testRender
     */
    public function testErrorsCodeDoesNotOverrideRendererCode()
    {
        $status = 501;
        $content = $this->encoder->encodeErrors($this->exception->getErrors()->getAll());

        $this->renderer->withStatusCode($status);
        $this->expectedResponse($content, $status);
        $this->assertResponse($this->renderer->render($this->exception));
    }

    /**
     * If rendered with a class that does not provide errors, expect no content. (I.e. swallow the problem to prevent
     * an additional exception to be caused.)
     */
    public function testRenderWithInvalidException()
    {
        $this->expectedResponse(null, 500);
        $this->assertResponse($this->renderer->render(new Exception()));
    }

    /**
     * If no encoder set, content should be null (i.e. swallow the problem so no additional exception is caused.)
     */
    public function testRenderWithoutEncoder()
    {
        $renderer = new ErrorsAwareRenderer($this->responses);
        $renderer->withMediaType($this->mediaType);

        $this->expectedResponse(null, self::STATUS);
        $this->assertResponse($renderer->render($this->exception));
    }
}
