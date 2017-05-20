<?php

/**
 * Copyright 2017 Cloud Creativity Limited
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

use InvalidArgumentException;
use stdClass;

/**
 * Class ObjectUtils
 *
 * @package CloudCreativity\JsonApi
 */
class ObjectUtils
{

    /**
     * @param object|array $data
     * @return array
     */
    public static function toArray($data)
    {
        if (!is_object($data) && !is_array($data)) {
            throw new InvalidArgumentException('Expecting an object or array to convert to an array.');
        }

        $arr = [];

        foreach ($data as $key => $value) {
            $arr[$key] = (is_object($value) || is_array($value)) ? static::toArray($value) : $value;
        }

        return $arr;
    }

    /**
     * @param $data
     * @param callable $transform
     * @return array|stdClass
     */
    public static function transformKeys($data, callable $transform)
    {
        if (!is_object($data) && !is_array($data)) {
            throw new InvalidArgumentException('Expecting an object or array to transform keys.');
        }

        $copy = is_object($data) ? clone $data : $data;

        foreach ($copy as $key => $value) {

            $transformed = call_user_func($transform, $key);
            $value = (is_object($value) || is_array($value)) ? self::transformKeys($value, $transform) : $value;

            if (is_object($data)) {
                unset($data->{$key});
                $data->{$transformed} = $value;
            } else {
                unset($data[$key]);
                $data[$transformed] = $value;
            }
        }

        return $data;
    }
}
