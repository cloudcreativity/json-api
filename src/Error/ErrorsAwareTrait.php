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

namespace CloudCreativity\JsonApi\Error;

use CloudCreativity\JsonApi\Contracts\Error\ErrorCollectionInterface;

/**
 * Class ErrorsAwareTrait
 * @package CloudCreativity\JsonApi
 */
trait ErrorsAwareTrait
{

    /**
     * @var ErrorCollectionInterface|null
     */
    protected $_errors;

    /**
     * @param ErrorCollectionInterface $errors
     * @return $this
     */
    public function setErrors(ErrorCollectionInterface $errors)
    {
        $this->_errors = $errors;

        return $this;
    }

    /**
     * @return ErrorCollectionInterface
     */
    public function getErrors()
    {
        if (!$this->_errors instanceof ErrorCollectionInterface) {
            $this->_errors = new ErrorCollection();
        }

        return $this->_errors;
    }
}