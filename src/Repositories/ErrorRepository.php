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

namespace CloudCreativity\JsonApi\Repositories;

use CloudCreativity\JsonApi\Contracts\Repositories\ErrorRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Utils\ReplacerInterface;
use CloudCreativity\JsonApi\Document\Error;

/**
 * Class ErrorRepository
 * @package CloudCreativity\JsonApi
 */
class ErrorRepository implements ErrorRepositoryInterface
{

    /**
     * @var ReplacerInterface|null
     */
    private $replacer;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * ErrorRepository constructor.
     * @param ReplacerInterface|null $replacer
     */
    public function __construct(ReplacerInterface $replacer = null)
    {
        $this->replacer = $replacer;
    }

    /**
     * Add error configuration.
     *
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        $this->errors = array_merge($this->errors, $config);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function error($key, array $values = [])
    {
        $errorArray = $this->template($key, $values);

        return Error::create($errorArray);
    }

    /**
     * @inheritdoc
     */
    public function errorWithPointer($key, $pointer, array $values = [])
    {
        $errorArray = $this->template($key, $values);
        $error = Error::create($errorArray);
        $error->setSourcePointer($pointer);

        return $error;
    }

    /**
     * @inheritdoc
     */
    public function errorWithParameter($key, $parameter, array $values = [])
    {
        $errorArray = $this->template($key, $values);
        $error = Error::create($errorArray);
        $error->setSourceParameter($parameter);

        return $error;
    }

    /**
     * @param $key
     * @param array $values
     * @return array
     */
    protected function template($key, array $values = [])
    {
        return $this->replace($this->get($key), $values);
    }

    /**
     * @param $key
     * @return array
     */
    protected function get($key)
    {
        return isset($this->errors[$key]) ? (array) $this->errors[$key] : [];
    }

    /**
     * @param array $error
     * @param array $values
     * @return array
     */
    protected function replace(array $error, array $values)
    {
        if (isset($error[Error::DETAIL]) && $this->replacer) {
            $error[Error::DETAIL] = $this->replacer->replace($error[Error::DETAIL], $values);
        }

        return $error;
    }

}
