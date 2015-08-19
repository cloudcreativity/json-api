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

namespace CloudCreativity\JsonApi\Validator\Resource;

use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Validator\Helper\AttributesValidatorTrait;
use CloudCreativity\JsonApi\Validator\Helper\AttributeTrait;
use CloudCreativity\JsonApi\Validator\Helper\RelationshipsValidatorTrait;
use CloudCreativity\JsonApi\Validator\Helper\RelationshipTrait;
use CloudCreativity\JsonApi\Validator\ResourceIdentifier\ExpectedIdValidator;
use CloudCreativity\JsonApi\Validator\ResourceIdentifier\ExpectedTypeValidator;

/**
 * Class ResourceObjectValidator
 * @package CloudCreativity\JsonApi
 */
class ResourceObjectValidator extends AbstractResourceObjectValidator
{

    use AttributesValidatorTrait,
        RelationshipsValidatorTrait,
        AttributeTrait,
        RelationshipTrait;

    /**
     * @var mixed
     */
    protected $_type;

    /**
     * @var mixed
     */
    protected $_id;

    /**
     * @param $type
     * @param $id
     */
    public function __construct($type = null, $id = null)
    {
        $this->type($type)->id($id);
    }

    /**
     * Set the expected type for the resource object.
     *
     * @param $type
     * @return $this
     */
    public function type($type)
    {
        $this->_type = $type;

        return $this;
    }

    /**
     * Set the expected id for the resource object.
     *
     * @param $id
     * @return $this
     */
    public function id($id)
    {
        $this->_id = $id;

        return $this;
    }

    /**
     * Add key or keys as allowed attributes.
     *
     * @param $keyOrKeys
     * @return $this
     */
    public function allowed($keyOrKeys)
    {
        $keys = is_array($keyOrKeys) ? $keyOrKeys : [$keyOrKeys];

        $attributes = $this->getKeyedAttributes();
        $relationships = $this->getKeyedRelationships();

        foreach ($keys as $key) {

            if ($relationships->hasValidator($key)) {
                $relationships->addAllowedKeys($key);
            } else {
                $attributes->addAllowedKeys($key);
            }
        }

        return $this;
    }

    /**
     * Set attributes and/or relationships to only accept keys for which there are validators.
     *
     * @param $attributes
     *      whether to restrict attributes
     * @param $relationships
     *      whether to restrict relationships
     * @return $this
     */
    public function restrict($attributes = true, $relationships = true)
    {
        if ($attributes) {
            $validator = $this->getKeyedAttributes();
            $validator->setAllowedKeys($validator->keys());
        }

        if ($relationships) {
            $validator = $this->getKeyedRelationships();
            $validator->setAllowedKeys($validator->keys());
        }

        return $this;
    }

    /**
     * Sets required keys, either on the attributes or the relationships.
     *
     * If a validator exists for a supplied key on the `RelationshipsValidator` instance, the key will be added as a
     * required relationship key. Otherwise it will set as required on the `AttributesValidator` instance.
     *
     * @param $keyOrKeys
     * @return $this
     */
    public function required($keyOrKeys)
    {
        $keys = is_array($keyOrKeys) ? $keyOrKeys : [$keyOrKeys];

        $attributes = $this->getKeyedAttributes();
        $relationships = $this->getKeyedRelationships();

        foreach ($keys as $key) {

            if ($relationships->hasValidator($key)) {
                $relationships->addRequiredKeys($key);
            } else {
                $attributes->addRequiredKeys($key);
            }
        }

        return $this;
    }

    /**
     * @return ExpectedTypeValidator
     */
    public function getTypeValidator()
    {
        return new ExpectedTypeValidator($this->_type);
    }

    /**
     * @return ExpectedIdValidator|null
     */
    public function getIdValidator()
    {
        return !is_null($this->_id) ? new ExpectedIdValidator($this->_id) : null;
    }

    /**
     * Get the validator for the specified relationship.
     *
     * @param $key
     * @return ValidatorInterface
     */
    public function getRelated($key)
    {
        return $this->getKeyedRelationships()->getValidator($key);
    }

}
