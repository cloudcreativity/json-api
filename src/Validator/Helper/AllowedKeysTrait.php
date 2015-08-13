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

namespace CloudCreativity\JsonApi\Validator\Helper;

/**
 * Class AllowedKeysTrait
 * @package CloudCreativity\JsonApi
 */
trait AllowedKeysTrait
{

    /**
     * @var array|null
     *      null means any allowed, array means only the contained keys are allowed.
     */
    protected $_allowedKeys;

    /**
     * @param $keyOrKeys
     * @return $this
     */
    public function setAllowedKeys($keyOrKeys)
    {
        $this->_allowedKeys = is_array($keyOrKeys) ? $keyOrKeys : [$keyOrKeys];

        return $this;
    }

    /**
     * @param $keyOrKeys
     * @return $this
     */
    public function addAllowedKeys($keyOrKeys)
    {
        $keys = is_array($keyOrKeys) ? $keyOrKeys : [$keyOrKeys];

        if (!is_array($this->_allowedKeys)) {
            $this->_allowedKeys = [];
        }

        $this->_allowedKeys = array_merge($this->_allowedKeys, $keys);

        return $this;
    }

    /**
     * @param $key
     * @return bool
     */
    public function isAllowedKey($key)
    {
        return is_array($this->_allowedKeys) ? in_array($key, $this->_allowedKeys) : true;
    }
}