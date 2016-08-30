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

namespace CloudCreativity\JsonApi\Object;

use CloudCreativity\JsonApi\Contracts\Object\DocumentInterface;
use CloudCreativity\JsonApi\Exceptions\RuntimeException;
use CloudCreativity\JsonApi\Object\Helpers\MetaMemberTrait;

/**
 * Class Document
 * @package CloudCreativity\JsonApi
 */
class Document extends StandardObject implements DocumentInterface
{

    use MetaMemberTrait;

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if (!$this->has(self::DATA)) {
            throw new RuntimeException('Data member is not present.');
        }

        $data = $this->get(self::DATA);

        if (!is_object($data)) {
            throw new RuntimeException('Data member is not an object or null.');
        }

        return new StandardObject($data);
    }

    /**
     * @inheritdoc
     */
    public function getResource()
    {
        $data = $this->getData();

        return new Resource($data->getProxy());
    }

    /**
     * @inheritdoc
     */
    public function getRelationship()
    {
        return new Relationship($this->getProxy());
    }
}
