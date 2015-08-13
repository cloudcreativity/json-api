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

use CloudCreativity\JsonApi\Validator\ResourceIdentifier\ExpectedIdValidator;
use CloudCreativity\JsonApi\Validator\ResourceIdentifier\ExpectedTypeValidator;

/**
 * Class ResourceObjectValidator
 * @package CloudCreativity\JsonApi
 */
class ResourceObjectValidator extends AbstractResourceObjectValidator
{

    use AttributesValidatorTrait,
        RelationshipsValidatorTrait;

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
     * Set an attribute of the resource object.
     *
     * @param $key
     * @param null $type
     * @param array $options
     * @return $this
     */
    public function attr($key, $type = null, array $options = [])
    {
        $this->getAttributes()
            ->attr($key, $type, $options);

        return $this;
    }

    /**
     * Set a belongs-to relationship for the resource object.
     *
     * @param $key
     * @param $typeOrTypes
     * @param array $options
     * @return $this
     */
    public function belongsTo($key, $typeOrTypes, array $options = [])
    {
        $this->getRelationships()
            ->belongsTo($key, $typeOrTypes, $options);

        return $this;
    }

    /**
     * Set a has-many relationship for the resource object.
     *
     * @param $key
     * @param $typeOrTypes
     * @param array $options
     * @return $this
     */
    public function hasMany($key, $typeOrTypes, array $options = [])
    {
        $this->getRelationships()
            ->hasMany($key, $typeOrTypes, $options);

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
        $this->getAttributes()
            ->addAllowedKeys($keyOrKeys);

        return $this;
    }

    /**
     * Set attributes to only accept keys for which there are validators.
     *
     * @return $this
     */
    public function restrict()
    {
        $this->getAttributes()->setRestricted();

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

        $attributes = $this->getAttributes();
        $relationships = $this->getRelationships();

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
     * @return \CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface
     */
    public function getRelated($key)
    {
        return $this->getRelationships()->getValidator($key);
    }
}