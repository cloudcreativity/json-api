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
use OutOfBoundsException;

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
    private $type;

    /**
     * @var mixed
     */
    private $id;

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
        $this->type = $type;

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
        $this->id = $id;

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

        $attributes = $this->hasKeyedAttributes() ? $this->getKeyedAttributes() : null;
        $relationships = $this->hasKeyedRelationships() ? $this->getKeyedRelationships() : null;

        foreach ($keys as $key) {

            if ($relationships && $relationships->hasValidator($key)) {
                $relationships->addAllowedKeys($key);
            } elseif ($attributes && $attributes->hasValidator($key)) {
                $attributes->addAllowedKeys($key);
            }
        }

        return $this;
    }

    /**
     * Helper method to set attribute and/or relationship key validators as required.
     *
     * @param $keyOrKeys
     * @return $this
     */
    public function required($keyOrKeys)
    {
        $keys = is_array($keyOrKeys) ? $keyOrKeys : [$keyOrKeys];

        foreach ($keys as $key) {

            $validator = $this->getKeyValidator($key);

            if (method_exists($validator, 'setRequired')) {
                $validator->setRequired(true);
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
     * @param $key
     * @return ValidatorInterface
     */
    public function getKeyValidator($key)
    {
        $attributes = $this->hasKeyedAttributes() ? $this->getKeyedAttributes() : null;

        if ($attributes && $attributes->hasValidator($key)) {
            return $attributes->getValidator($key);
        }

        $relationships = $this->hasKeyedRelationships() ? $this->getKeyedRelationships() : null;

        if ($relationships && $relationships->hasValidator($key)) {
            return $relationships->getValidator($key);
        }

        throw new OutOfBoundsException(sprintf('Key "%s" does not exist as a validator on attributes or relationships.', $key));
    }

    /**
     * @return ExpectedTypeValidator
     */
    public function getTypeValidator()
    {
        return new ExpectedTypeValidator($this->type);
    }

    /**
     * @return ExpectedIdValidator|null
     */
    public function getIdValidator()
    {
        return !is_null($this->id) ? new ExpectedIdValidator($this->id) : null;
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

    /**
     * @return bool
     */
    public function isRequired()
    {
        return true;
    }

}
