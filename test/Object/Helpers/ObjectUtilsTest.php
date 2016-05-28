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

namespace CloudCreativity\JsonApi\Object\Helpers;

use CloudCreativity\JsonApi\TestCase;
use stdClass;

class ObjectUtilsTest extends TestCase
{

    public function testToArray()
    {
        $object = <<<OBJ
        {
            "multiple": [
                {
                    "val1": "one",
                    "val2": "two"
                },
                {
                    "val3": "three",
                    "val4": [
                        {
                            "val4": "four"
                        }
                    ]
                }
            ],
            "nested": {
                "obj1": {
                    "val1": "one",
                    "val2": "two"
                },
                "obj2": {
                    "val3": "three",
                    "val4": "four"
                }
            }
        }
OBJ;

        $expected = [
            "multiple" => [
                [
                    "val1" => "one",
                    "val2" => "two",
                ],
                [
                    "val3" => "three",
                    "val4" => [
                        [
                            "val4" => "four",
                        ],
                    ],
                ],
            ],
            "nested" => [
                "obj1" => [
                    "val1" => "one",
                    "val2" => "two",
                ],
                "obj2" => [
                    "val3" => "three",
                    "val4" => "four",
                ],
            ],
        ];

        $actual = ObjectUtils::toArray($this->toObject($object));

        $this->assertEquals($expected, $actual);
    }

    public function testTransformKeys()
    {
        $object = <<<OBJ
        {
            "first-name": "Frankie",
            "surname": "Manning",
            "meta": {
                "created-at": "2015-01-01",
                "updated-at": "2015-02-01"
            },
            "associates": [
                {
                    "first-name": "Ella",
                    "surname": "Fitzgerald"
                },
                {
                    "first-name": "Chick",
                    "surname": "Webb"
                }
            ]
        }
OBJ;

        $expected = <<<OBJ
        {
            "first_name": "Frankie",
            "surname": "Manning",
            "meta": {
                "created_at": "2015-01-01",
                "updated_at": "2015-02-01"
            },
            "associates": [
                {
                    "first_name": "Ella",
                    "surname": "Fitzgerald"
                },
                {
                    "first_name": "Chick",
                    "surname": "Webb"
                }
            ]
        }
OBJ;

        $object = $this->toObject($object);
        $actual = ObjectUtils::transformKeys($object, function ($key) {
            return is_int($key) ? $key : str_replace('-', '_', $key);
        });

        $this->assertEquals($this->toObject($expected), $actual);
        $this->assertSame($object, $actual);
        $this->assertSame($object->meta, $actual->meta);
    }

    /**
     * @param $string
     * @return stdClass
     */
    private function toObject($string)
    {
        $object = json_decode($string);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $this->fail("Error in JSON: \n" . $string);
        }

        return $object;
    }
}
