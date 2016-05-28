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

namespace CloudCreativity\JsonApi\Validators;

use CloudCreativity\JsonApi\Contracts\Store\StoreInterface;
use CloudCreativity\JsonApi\TestCase as BaseTestCase;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Exceptions\ErrorCollection;
use PHPUnit_Framework_MockObject_MockObject;

class TestCase extends BaseTestCase
{

    /**
     * @var ValidationMessageFactory
     */
    protected $messages;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $store;

    /**
     * @var ValidatorFactory
     */
    protected $factory;

    /**
     * @return void
     */
    protected function setUp()
    {
        /** @var StoreInterface $store */
        $store = $this->getMockBuilder(StoreInterface::class)->getMock();

        $config = require __DIR__ . '/../../config/validation.php';
        $this->messages = new ValidationMessageFactory($config);
        $this->store = $store;
        $this->factory = new ValidatorFactory($this->messages, $store);
    }

    /**
     * @param ErrorCollection $errors
     * @param $pointer
     * @param $messageKey
     */
    protected function assertErrorAt(ErrorCollection $errors, $pointer, $messageKey)
    {
        $error = $this->findErrorAt($errors, $pointer);
        $expected = $this->messages->error($messageKey);

        $this->assertEquals($expected->getId(), $error->getId(), 'Unexpected error id.');
        $this->assertEquals($expected->getTitle(), $error->getTitle(), 'Unexpected error title.');
        $this->assertEquals($expected->getCode(), $error->getCode(), 'Unexpected error code.');
        $this->assertEquals($expected->getStatus(), $error->getStatus(), 'Unexpected error status.');
    }

    /**
     * @param ErrorCollection $errors
     * @param $pointer
     * @param $needle
     */
    protected function assertDetailContains(ErrorCollection $errors, $pointer, $needle)
    {
        $error = $this->findErrorAt($errors, $pointer);

        $this->assertContains($needle, $error->getDetail(), "Invalid detail for error: $pointer");
    }

    /**
     * @param ErrorCollection $errors
     * @param $pointer
     * @return ErrorInterface|null
     */
    protected function findErrorAt(ErrorCollection $errors, $pointer)
    {
        /** @var ErrorInterface $error */
        foreach ($errors as $error) {
            $source = (array) $error->getSource();
            $check = isset($source[ErrorInterface::SOURCE_POINTER])
                ? $source[ErrorInterface::SOURCE_POINTER] : null;

            if ($pointer === $check) {
                return $error;
            }
        }

        $pointers = implode(', ', $this->pointers($errors));
        $this->fail("$pointer not in pointers: [$pointers]");

        return null;
    }

    /**
     * @param ErrorCollection $errors
     * @return array
     */
    protected function pointers(ErrorCollection $errors)
    {
        $pointers = [];

        /** @var ErrorInterface $error */
        foreach ($errors as $error) {
            $source = (array) $error->getSource();
            $pointer = isset($source[ErrorInterface::SOURCE_POINTER])
                ? $source[ErrorInterface::SOURCE_POINTER] : null;

            if ($pointer) {
                $pointers[] = $pointer;
            }
        }

        return array_unique($pointers);
    }
}
