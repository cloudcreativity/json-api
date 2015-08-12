<?php

namespace CloudCreativity\JsonApi\Object\Relationships;

use CloudCreativity\JsonApi\Object\StandardObject;

class Relationships extends StandardObject
{

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return parent::get($key);
    }

    /**
     * @param $key
     * @param $default
     * @return Relationship
     */
    public function get($key, $default = null)
    {
        return new Relationship(parent::get($key, $default));
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
}
