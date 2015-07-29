<?php

namespace CloudCreativity\JsonApi\Resource;

use CloudCreativity\JsonApi\Resource\Attributes\Attributes;
use CloudCreativity\JsonApi\Resource\Identifier\Identifier;
use CloudCreativity\JsonApi\Resource\Relationships\Relationships;

class ResourceObject implements \ArrayAccess
{

    const TYPE = Identifier::TYPE;
    const ID = Identifier::ID;
    const ATTRIBUTES = 'attributes';
    const RELATIONSHIPS = 'relationships';

    /**
     * @var array
     */
    protected $_input;

    /**
     * @param array $input
     */
    public function __construct(array $input)
    {
        $this->_input = $input;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_input);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->_input[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->_input[$offset] = $value;
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->_input[$offset]);
    }

    /**
     * @return Identifier
     */
    public function getIdentifier()
    {
        $type = isset($this[static::TYPE]) ? $this[static::TYPE] : null;
        $id = isset($this[static::ID]) ? $this[static::ID] : null;

        return new Identifier($type, $id);
    }

    /**
     * @return Attributes
     */
    public function getAttributes()
    {
        $arr = isset($this[static::ATTRIBUTES]) ? $this[static::ATTRIBUTES] : [];

        return new Attributes($arr);
    }

    /**
     * @return Relationships
     */
    public function getRelationships()
    {
        $arr = isset($this[static::RELATIONSHIPS]) ? $this[static::RELATIONSHIPS] : [];

        return new Relationships($arr);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->_input;
    }
}
