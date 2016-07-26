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

use CloudCreativity\JsonApi\Object\Helpers\ObjectUtils;
use stdClass;
use PHPUnit_Framework_Assert as PHPUnit;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface as Keys;

/**
 * Class ResourceTester
 * @package CloudCreativity\JsonApi
 */
class ResourceTester
{

    /**
     * @var stdClass
     */
    private $resource;

    /**
     * @var int|null
     */
    private $index;

    /**
     * ResourceTester constructor.
     * @param stdClass $resource
     * @param int|null $index
     *      if the resource appears within a collection, its index within that collection.
     */
    public function __construct(stdClass $resource, $index = null)
    {
        $this->resource = $resource;
        $this->index = $index;
        $this->isComplete();
    }

    /**
     * @return stdClass
     */
    public function getResource()
    {
        return clone $this->resource;
    }

    /**
     * @return int|null
     */
    public function getIndex()
    {
        return is_int($this->index) ? $this->index : null;
    }

    /**
     * @param $type
     * @param $id
     * @return bool
     */
    public function is($type, $id)
    {
        return $this->getType() === $type && $this->getId() == $id;
    }

    /**
     * Assert that the resource matches the expected type and id.
     *
     * @param $type
     * @param $id
     * @param string|null $message
     * @return $this
     */
    public function assertIs($type, $id, $message = null)
    {
        $expected = sprintf('%s:%s', $type, $id);
        $actual = sprintf('%s:%s', $this->getType(), $this->getId());
        $message = $message ?: "Resource [$actual] does not match expected resource [$expected]";

        PHPUnit::assertTrue($this->is($type, $id), $this->withIndex($message));

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return isset($this->resource->{Keys::KEYWORD_TYPE}) ?
            $this->resource->{Keys::KEYWORD_TYPE} : null;
    }

    /**
     * Assert that the resource has a type member.
     *
     * @param string|null $message
     * @return $this
     */
    public function assertType($message = null)
    {
        $actual = $this->getType();
        $message = $message ?: 'Resource does not have a type';
        PHPUnit::assertTrue(is_string($actual) && !empty($actual), $this->withIndex($message));

        return $this;
    }

    /**
     * Assert that the resource type matches the expected type.
     *
     * @param $expected
     * @param string|null $message
     * @return $this
     */
    public function assertTypeIs($expected, $message = null)
    {
        $actual = $this->getResource();
        $message = $message ?: sprintf('Unexpected resource type [%s]', $actual);
        PHPUnit::assertEquals($expected, $actual, $this->withIndex($message));

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return isset($this->resource->{Keys::KEYWORD_ID}) ?
            $this->resource->{Keys::KEYWORD_ID} : null;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return isset($this->resource->{Keys::KEYWORD_ATTRIBUTES}) ?
            $this->resource->{Keys::KEYWORD_ATTRIBUTES} : null;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $attributes = $this->getAttributes();

        return is_object($attributes) && isset($attributes->{$key}) ? $attributes->{$key} : null;
    }

    /**
     * Assert that an attribute value is equal to the expected value.
     *
     * @param string $key
     * @param mixed $expected
     * @param string|null $message
     * @return $this
     */
    public function assertAttribute($key, $expected, $message = null)
    {
        $message = $message ?: "Unexpected attribute [$key]";
        $actual = $this->getAttribute($key);
        PHPUnit::assertEquals($expected, $actual, $this->withIndex($message));

        return $this;
    }

    /**
     * Assert that an attribute value is the same as the expected value.
     *
     * @param string $key
     * @param mixed $expected
     * @param string|null $message
     * @return $this
     */
    public function assertAttributeIs($key, $expected, $message = null)
    {
        $message = $message ?: "Unexpected attribute [$key]";
        $actual = $this->getAttribute($key);
        PHPUnit::assertSame($expected, $actual, $this->withIndex($message));

        return $this;
    }

    /**
     * Assert that the resource's attributes contains the provided subset.
     *
     * @param object|array $expected
     * @param string|null $message
     * @return $this
     */
    public function assertAttributesSubset($expected, $message = null)
    {
        $expected = ObjectUtils::toArray($expected);
        $actual = ObjectUtils::toArray($this->getAttributes() ?: []);
        $message = $message ?
            $this->withIndex($message) :
            $this->withIndex('Unexpected resource attributes') . ': ' . json_encode($actual);

        PHPUnit::assertArraySubset($expected, $actual, $message);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRelationships()
    {
        return isset($this->resource->{Keys::KEYWORD_RELATIONSHIPS}) ?
            $this->resource->{Keys::KEYWORD_RELATIONSHIPS} : null;
    }

    /**
     * Assert that the resource's relationships contains the provided subset.
     *
     * @param object|array $expected
     * @param string|null $message
     * @return $this
     */
    public function assertRelationshipsSubset($expected, $message = null)
    {
        $expected = ObjectUtils::toArray($expected);
        $actual = ObjectUtils::toArray($this->getRelationships() ?: []);
        $message = $message ?
            $this->withIndex($message) :
            $this->withIndex('Unexpected resource relationships') . ': ' . json_encode($actual);

        PHPUnit::assertArraySubset($expected, $actual, $message);

        return $this;
    }

    /**
     * @param $message
     * @return string
     */
    protected function withIndex($message)
    {
        if (is_int($this->index)) {
            $message .= " at index [$this->index]";
        }

        return $message;
    }

    /**
     * @return void
     */
    private function isComplete()
    {
        $type = $this->getType();

        if (!is_string($type) || empty($type)) {
            PHPUnit::fail($this->withIndex('Resource does not have a type member'));
        }

        $id = $this->getId();

        if (!is_string($id) && !is_int($id)) {
            PHPUnit::fail($this->withIndex('Resource does not have an id member'));
        } elseif (is_string($id) && empty($id)) {
            PHPUnit::fail($this->withIndex('Resource has an empty string id member'));
        }
    }
}
