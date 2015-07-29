<?php

namespace CloudCreativity\JsonApi\Resource\Relationships;

class Relationships implements \IteratorAggregate, \Countable, \ArrayAccess
{

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
        return $this->has($offset);
    }

    /**
     * @param mixed $offset
     * @return RelationshipObject
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        if ($value instanceof RelationshipObject) {
            $value = $value->toArray();
        } elseif (!is_array($value)) {
            throw new \InvalidArgumentException('Expecting an array or RelationshipObject.');
        }

        $this->_input[$key] = $value;

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function remove($key)
    {
        unset($this->_input[$key]);

        return $this;
    }

    /**
     * @param $key
     * @return RelationshipObject
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new \RuntimeException(sprintf('Relationship key "%s" does not exist.', $key));
        }

        return new RelationshipObject($this->_input[$key]);
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->_input);
    }

    /**
     * @param array $keys
     * @return bool
     */
    public function hasAll(array $keys)
    {
        foreach ($keys as $key) {

            if (!$this->has($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $keys
     * @return bool
     */
    public function hasAny(array $keys)
    {
        foreach ($keys as $key) {

            if ($this->has($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $keys
     * @return bool
     */
    public function hasOnly(array $keys)
    {
        foreach ($this->keys() as $key) {

            if (!in_array($key, $keys)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->_input);
    }

    /**
     * @return \Generator
     */
    public function getIterator()
    {
        foreach ($this->keys() as $key) {
            yield $key => $this->get($key);
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->_input);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->_input;
    }
}
