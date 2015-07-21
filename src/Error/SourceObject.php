<?php

namespace Appativity\JsonApi\Error;

class SourceObject implements \ArrayAccess, \JsonSerializable
{

    const POINTER = 'pointer';
    const PARAMETER = 'parameter';

    /**
     * @var array
     */
    protected $_data;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->_data = $data;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_data);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->_data[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    /**
     * @param $pointer
     * @return $this
     */
    public function setPointer($pointer)
    {
        if ($pointer instanceof \Closure) {
            $pointer = $pointer($this->getPointer());
        }

        $this[static::POINTER] = $pointer;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPointer()
    {
        return $this->offsetExists(static::POINTER) ? (string) $this[static::POINTER] : null;
    }

    /**
     * @param $parameter
     * @return $this
     */
    public function setParameter($parameter)
    {
        $this[static::PARAMETER] = $parameter;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getParameter()
    {
        return $this->offsetExists(static::PARAMETER) ? (string) $this[static::PARAMETER] : null;
    }

    /**
     * @param array $input
     * @return $this
     */
    public function exchangeArray(array $input)
    {
        $this->_data = array_merge($this->_data, $input);

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }

    /**
     * @return object
     */
    public function jsonSerialize()
    {
        return (object) $this->toArray();
    }
}
