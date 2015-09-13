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

use CloudCreativity\JsonApi\Contracts\Validator\KeyedValidatorInterface;
use CloudCreativity\JsonApi\Validator\Relationships\HasOneValidator;
use CloudCreativity\JsonApi\Validator\Relationships\HasManyValidator;

/**
 * Class RelationshipTrait
 * @package CloudCreativity\JsonApi
 */
trait RelationshipTrait
{

    /**
     * @return KeyedValidatorInterface
     */
    abstract public function getKeyedRelationships();

    /**
     * Helper method to add a belongs to validator for the specified key.
     *
     * @param $key
     * @param $typeOrTypes
     * @param array $options
     * @return $this
     */
    public function belongsTo($key, $typeOrTypes, array $options = [])
    {
        $relationships = $this->getKeyedRelationships();

        $validator = new HasOneValidator($typeOrTypes);
        $validator->configure($options);

        $relationships->setValidator($key, $validator);

        return $this;
    }

    /**
     * Helper method to add a has-many validator for the specified key.
     *
     * @param $key
     * @param $typeOrTypes
     * @param array $options
     * @return $this
     */
    public function hasMany($key, $typeOrTypes, array $options = [])
    {
        $relationships = $this->getKeyedRelationships();

        $validator = new HasManyValidator($typeOrTypes);
        $validator->configure($options);

        $relationships->setValidator($key, $validator);

        return $this;
    }
}
