<?php

namespace CloudCreativity\JsonApi\Object\Resource;

use CloudCreativity\JsonApi\Object\StandardObject;
use CloudCreativity\JsonApi\Object\Relationships\Relationships;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;

class Resource extends StandardObject
{

  const TYPE = 'type';
  const ID = 'id';
  const ATTRIBUTES = 'attributes';
  const RELATIONSHIPS = 'relationships';
  const META = 'meta';

  /**
   * @return string
   */
  public function getType()
  {
    $type = $this->get(static::TYPE);

    if (!is_string($type) || empty($type)) {
      throw new \RuntimeException('No resource object type set.');
    }

    return $type;
  }

  /**
   * @return mixed
   */
  public function getId()
  {
    if (!$this->hasId()) {
      throw new \RuntimeException('No resource object id set.');
    }

    return $this->get(static::ID);
  }

  /**
   * @return bool
   */
  public function hasId()
  {
    return $this->has(static::ID);
  }

  /**
   * @return ResourceIdentifier
   */
  public function getIdentifier()
  {
    $identifier = new ResourceIdentifier();
    $identifier->setType($this->getType());

    if ($this->hasId()) {
      $identifier->setId($this->getId());
    }

    return $identifier;
  }

  /**
   * @return StandardObject
   */
  public function getAttributes()
  {
    return new StandardObject($this->get(static::ATTRIBUTES));
  }

  /**
   * @return bool
   */
  public function hasAttributes()
  {
    return $this->has(static::ATTRIBUTES);
  }

  /**
   * @return Relationships
   */
  public function getRelationships()
  {
    return new Relationships($this->get(static::RELATIONSHIPS));
  }

  /**
   * @return bool
   */
  public function hasRelationships()
  {
    return $this->has(static::RELATIONSHIPS);
  }

  /**
   * @return StandardObject
   */
  public function getMeta()
  {
    return new StandardObject($this->get(static::META));
  }

  /**
   * @return bool
   */
  public function hasMeta()
  {
    return $this->has(static::META);
  }
}
