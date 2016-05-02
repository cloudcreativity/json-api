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

namespace CloudCreativity\JsonApi\Object;

use CloudCreativity\JsonApi\TestCase;
use stdClass;

class StandardObjectTest extends TestCase
{

    const KEY_A = 'foo';
    const VALUE_A = 'bar';

    const KEY_B = 'baz';
    const VALUE_B = 'bat';

    const KEY_C = 'foobar';

    public function testConstruct()
    {
        $proxy = new stdClass();
        $proxy->{static::KEY_A} = static::VALUE_A;

        $object = new StandardObject($proxy);

        $this->assertEquals(static::VALUE_A, $object->{static::KEY_A});

        return $object;
    }

    /**
     * @depends testConstruct
     */
    public function testSet(StandardObject $object)
    {
        $object->{static::KEY_B} = static::VALUE_B;

        $this->assertEquals(static::VALUE_B, $object->{static::KEY_B});
    }

    /**
     * @depends testConstruct
     */
    public function testIsset(StandardObject $object)
    {
        $this->assertTrue(isset($object->{static::KEY_A}));
        $this->assertFalse(isset($object->{static::KEY_C}));
    }

    /**
     * @depends testConstruct
     */
    public function testUnset(StandardObject $object)
    {
        unset($object->{static::KEY_A});
        $this->assertFalse(isset($object->{static::KEY_A}));
    }

    public function testIterator()
    {
        $arr = [
            static::KEY_A => static::VALUE_A,
            static::KEY_B => static::VALUE_B,
        ];

        $object = new StandardObject();
        $object->exchangeArray($arr);

        $this->assertEquals($arr, iterator_to_array($object));

        return $object;
    }

    /**
     * @depends testIterator
     */
    public function testCount(StandardObject $object)
    {
        $this->assertEquals(count($object->toArray()), count($object));
    }
}
