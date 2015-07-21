<?php

namespace Appativity\JsonApi\Resource\Attributes;

class Attributes implements \IteratorAggregate, \Countable, \ArrayAccess
{

    /**
     * @var array
     */
    protected $_data = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->setMany($data);
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
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
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
        $this->_data[$key] = $value;

        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function setMany(array $values)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function remove($key)
    {
        unset($this->_data[$key]);

        return $this;
    }

    /**
     * @param $key
     * @param $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->_data) ? $this->_data[$key] : $default;
    }

    /**
     * @param array $keys
     * @param $default
     * @return array
     */
    public function getMany(array $keys, $default = null)
    {
        $ret = [];

        foreach ($keys as $key) {
            $ret[$key] = $this->get($key, $default);
        }

        return $ret;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->_data;
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->_data);
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->_data);
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
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getAll());
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->_data);
    }

}
