<?php

namespace CloudCreativity\JsonApi\Object;

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

class ObjectUtilsTest extends \PHPUnit_Framework_TestCase
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
        $object = json_decode($object);

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

        $actual = ObjectUtils::toArray($object);

        $this->assertEquals($expected, $actual);
    }
}
