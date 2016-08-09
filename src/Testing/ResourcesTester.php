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

use Generator;
use PHPUnit_Framework_Assert as PHPUnit;

/**
 * Class ResourcesTester
 * @package CloudCreativity\JsonApi
 */
class ResourcesTester extends AbstractTraversableTester
{

    /**
     * @var array
     */
    private $stack;

    /**
     * ResourcesTester constructor.
     * @param array $resources
     */
    public function __construct(array $resources)
    {
        $this->stack = $resources;
    }

    /**
     * @return Generator
     */
    public function getIterator()
    {
        foreach ($this->stack as $index => $resource) {

            if (!is_object($resource)) {
                PHPUnit::fail(sprintf('Encountered a resource that is not an object at index %d', $index));
            }

            yield $index => new ResourceTester($resource, $index);
        }
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->stack);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->stack);
    }

    /**
     * @return array
     */
    public function getIdentifiers()
    {
        $identifiers = $this->reduce(function (array $carry, ResourceTester $resource) {
            $type = $resource->getType();

            if (!isset($carry[$type])) {
                $carry[$type] = [];
            }

            $carry[$type][] = $resource->getId();

            return $carry;
        }, []);

        return $this->normalizeIdentifiers($identifiers);
    }

    /**
     * Assert that the collection only contains the provided resource type(s)
     *
     * @param string|string[] $types
     * @return $this
     */
    public function assertTypes($types)
    {
        $expected = array_combine((array) $types, (array) $types);
        $actual = [];

        /** @var ResourceTester $resource */
        foreach ($this as $resource) {
            $type = $resource->getType();
            $actual[$type] = $type;
        }

        PHPUnit::assertEquals($expected, $actual, 'Unexpected resource types in data collection.');

        return $this;
    }

    /**
     * Assert that the collection contains the specified resource.
     *
     * @param string $type
     * @param string|int $id
     * @param string|null $message
     * @return $this
     */
    public function assertContainsResource($type, $id, $message = null)
    {
        $this->assertResource($type, $id, $message);

        return $this;
    }

    /**
     * Assert that the collection contains the specified resource, and return a resource tester.
     *
     * @param string $type
     * @param string|int $id
     * @param string|null $message
     * @return ResourceTester|null
     */
    public function assertResource($type, $id, $message = null)
    {
        $match = null;

        /** @var ResourceTester $resource */
        foreach ($this as $resource) {
            if ($resource->is($type, $id)) {
                $match = $resource;
                break;
            }
        }

        $message = $message ?: "Expected resource [$type:$id] does not exist in collection.";
        PHPUnit::assertNotNull($match, $message);

        return $match;
    }

    /**
     * Assert that the collection only contains the specified resources.
     *
     * The expected values must be keyed by resource type, and contain either a string id or an array
     * of string ids per type. For example:
     *
     * `['posts' => ['1', '2'], 'comments' => '1']`
     *
     * The order in which resources appear is ignored. The assertion will
     * fail if any of the supplied resources do not appear, or if the
     * collection contains any additional resources.
     *
     * @param array $expected
     * @param string|null $message
     * @return $this
     */
    public function assertContainsOnly(array $expected, $message = null)
    {
        $actual = $this->getIdentifiers();
        $message = $message ?: sprintf(
            'Collection contains [%s] resources, expecting [%s]',
            json_encode($actual),
            json_encode($expected)
        );

        PHPUnit::assertEquals($this->normalizeIdentifiers($expected), $actual, $message);

        return $this;
    }

    /**
     * @param array $identifiers
     * @return array
     */
    private function normalizeIdentifiers(array $identifiers)
    {
        return array_map(function ($ids) {
            $ids = (array) $ids;
            sort($ids);
            return $ids;
        }, $identifiers);
    }

}
