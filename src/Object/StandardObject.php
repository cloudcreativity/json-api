<?php

namespace CloudCreativity\JsonApi\Object;

class StandardObject implements \IteratorAggregate, \Countable
{

    use ObjectProxyTrait;

    /**
     * @param object|null $proxy
     */
    public function __construct($proxy = null)
    {
        if (!is_null($proxy)) {
            $this->setProxy($proxy);
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if (!$this->has($key)) {
            throw new \OutOfBoundsException(sprintf('Key "%s" does not exist.', $key));
        }

        return $this->get($key);
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * @param $key
     */
    public function __unset($key)
    {
        $this->remove($key);
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        if ($this->getProxy() instanceof \Traversable) {
            return $this->getProxy();
        }

        return new \ArrayIterator($this->toArray());
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->toArray());
    }
}