<?php

namespace Appativity\JsonApi\Resource\Relationships;

use Appativity\JsonApi\Resource\Identifier\Identifier;
use Appativity\JsonApi\Resource\Identifier\IdentifierCollection;

class RelationshipObject implements \ArrayAccess
{

    const DATA = 'data';

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
     * @return Identifier|IdentifierCollection|null
     */
    public function getData()
    {
        $data = isset($this[static::DATA]) ? $this[static::DATA] : null;

        if (!is_array($data)) {
            return null;
        }

        return (empty($data) || isset($data[0])) ? IdentifierCollection::create($data) : Identifier::create($data);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->_input;
    }

    /**
     * @return bool
     */
    public function isBelongsTo()
    {
        $data = $this->getData();

        return is_null($data) || $data instanceof Identifier;
    }

    /**
     * @return bool
     */
    public function isHasMany()
    {
        return $this->getData() instanceof IdentifierCollection;
    }
}
