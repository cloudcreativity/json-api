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

namespace CloudCreativity\JsonApi\Decoders;

use CloudCreativity\JsonApi\Error\MultiErrorException;
use CloudCreativity\JsonApi\Object\Resource\Resource;
use CloudCreativity\JsonApi\Validator\ValidatorAwareTrait;

/**
 * Class ResourceDecoder
 * @package CloudCreativity\JsonApi
 */
class ResourceDecoder extends AbstractDecoder
{

    use ValidatorAwareTrait;

    /**
     * @param string $content
     * @return Resource
     * @throws MultiErrorException
     */
    public function decode($content)
    {
        $content = $this->parseJson($content);
        $validator = $this->getValidator();

        if (!$validator->isValid($content)) {
            throw new MultiErrorException($validator->getErrors(), 'Invalid request body content.');
        }

        return new Resource($content);
    }
}