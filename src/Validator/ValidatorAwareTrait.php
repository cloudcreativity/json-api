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

namespace CloudCreativity\JsonApi\Validator;

use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;

/**
 * Class ValidatorAwareTrait
 * @package CloudCreativity\JsonApi
 */
trait ValidatorAwareTrait
{

    /**
     * @var ValidatorInterface|null
     */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        if (!$this->validator instanceof ValidatorInterface) {
            throw new \RuntimeException(sprintf('%s expects to be injected with a %s instance.', static::class, ValidatorInterface::class));
        }

        return $this->validator;
    }

    /**
     * @return bool
     */
    public function hasValidator()
    {
        return $this->validator instanceof ValidatorInterface;
    }
}
