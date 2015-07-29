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
   * @return Relationship
   */
  public function get($key)
  {
    return new Relationship(parent::get($key));
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
