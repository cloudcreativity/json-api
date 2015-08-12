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

namespace CloudCreativity\JsonApi\Validator\Type;

use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;
use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Validator\AbstractValidator;

/**
 * Class TypeValidator
 * @package CloudCreativity\JsonApi
 */
class TypeValidator extends AbstractValidator implements ConfigurableInterface
{

    use NullableTrait;

    // Config Constants
    const ACCEPT_NULL = 'acceptNull';

    // Error Constants
    const ERROR_INVALID_VALUE = 'invalid-value';

    /**
     * @var array
     */
    protected $templates = [
        self::ERROR_INVALID_VALUE => [
            ErrorObject::CODE => self::ERROR_INVALID_VALUE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Value',
        ],
    ];

    /**
     * @param bool $nullable
     */
    public function __construct($nullable = false)
    {
        $this->setAcceptNull($nullable);
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        if (array_key_exists(static::ACCEPT_NULL, $config)) {
            $this->setAcceptNull($config[static::ACCEPT_NULL]);
        }

        return $this;
    }

    /**
     * @param $value
     */
    protected function validate($value)
    {
        if (is_null($value) && $this->isNullAllowed()) {
            return;
        }

        if (!$this->isType($value)) {
            $this->error(static::ERROR_INVALID_VALUE);
        }
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isType($value)
    {
        return true;
    }
}