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

namespace CloudCreativity\JsonApi\Exceptions;

use CloudCreativity\JsonApi\Document\Error;
use CloudCreativity\JsonApi\TestCase;
use Neomerx\JsonApi\Document\Error as BaseError;
use Neomerx\JsonApi\Exceptions\ErrorCollection as BaseCollection;

/**
 * Class ErrorCollectionTest
 * @package CloudCreativity\JsonApi
 */
final class ErrorCollectionTest extends TestCase
{

    public function testIterator()
    {
        $a = new BaseError(null, null, 422);
        $b = new BaseError(null, null, 500);
        $c = new Error(null, null, 400);

        $expected = [Error::cast($a), Error::cast($b), $c];
        $errors = new ErrorCollection([$a, $b, $c]);

        $this->assertSame([$a, $b, $c], $errors->getArrayCopy());
        $this->assertEquals($expected, iterator_to_array($errors));
    }

    public function testMerge()
    {
        $a = new BaseError(null, null, 422);
        $b = new Error(null, null, 400);
        $c = new BaseError(null, null, 500);

        $merge = new BaseCollection();
        $merge->add($a)->add($b);

        $errors = new ErrorCollection([$c]);
        $expected = [$c, $a, $b];

        $this->assertSame($errors, $errors->merge($merge));
        $this->assertSame($expected, $errors->getArrayCopy());
    }

    public function testCastReturnsSame()
    {
        $errors = new ErrorCollection();
        $this->assertSame($errors, ErrorCollection::cast($errors));
    }

    public function testCastError()
    {
        $error = new BaseError(null, null, 422);
        $expected = new ErrorCollection([$error]);
        $this->assertEquals($expected, ErrorCollection::cast($error));
    }

    public function testCastBaseCollection()
    {
        $error = new Error(null, null, 422);
        $expected = new ErrorCollection([$error]);
        $base = new BaseCollection();
        $base->add($error);
        $this->assertEquals($expected, ErrorCollection::cast($base));
    }

    public function testCastArray()
    {
        $arr = [new Error(null, null, 500)];
        $expected = new ErrorCollection($arr);
        $this->assertEquals($expected, ErrorCollection::cast($arr));
    }
}
