<?php

namespace CloudCreativity\JsonApi\Validator\Relationships;

use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Validator\AbstractValidator;
use CloudCreativity\JsonApi\Object\Relationships\Relationship;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifierCollection;

class HasManyValidator extends AbstractValidator
{

  const ERROR_INVALID_VALUE = BelongsToValidator::ERROR_INVALID_VALUE;
  const ERROR_INVALID_TYPE = BelongsToValidator::ERROR_INVALID_TYPE;
  const ERROR_INVALID_ID = BelongsToValidator::ERROR_INVALID_ID;
  const ERROR_EMPTY_DISALLOWED = BelongsToValidator::ERROR_NULL_DISALLOWED;
  const ERROR_INVALID_COLLECTION = 'invalid-resources';
  const ERROR_NOT_FOUND = BelongsToValidator::ERROR_NOT_FOUND;

  protected $templates = [
    self::ERROR_INVALID_VALUE => [
      ErrorObject::CODE => self::ERROR_INVALID_VALUE,
      ErrorObject::STATUS => 400,
      ErrorObject::TITLE => 'Invalid Value',
      ErrorObject::DETAIL => 'Value provided is invalid for a belongs-to relationship.',
    ],
    self::ERROR_INVALID_TYPE => [
      ErrorObject::CODE => self::ERROR_INVALID_TYPE,
      ErrorObject::STATUS => 400,
      ErrorObject::TITLE => 'Invalid Relationship',
      ErrorObject::DETAIL => 'This has-many relationship does not accept the specified resource object type.',
    ],
    self::ERROR_INVALID_ID => [
      ErrorObject::CODE => self::ERROR_INVALID_ID,
      ErrorObject::STATUS => 400,
      ErrorObject::TITLE => 'Invalid Relationship',
      ErrorObject::DETAIL => 'The supplied relationship id is missing or invalid.',
    ],
    self::ERROR_EMPTY_DISALLOWED => [
      ErrorObject::CODE => self::ERROR_EMPTY_DISALLOWED,
      ErrorObject::STATUS => 422,
      ErrorObject::TITLE => 'Invalid Relationship',
      ErrorObject::DETAIL => 'This relationship cannot be set to an empty value.',
    ],
    self::ERROR_INVALID_COLLECTION => [
      ErrorObject::CODE => self::ERROR_INVALID_COLLECTION,
      ErrorObject::STATUS => 400,
      ErrorObject::TITLE => 'Invalid Relationship',
      ErrorObject::DETAIL => 'The has-many relationships provided is invalid.',
    ],
    self::ERROR_NOT_FOUND => [
      ErrorObject::CODE => self::ERROR_NOT_FOUND,
      ErrorObject::STATUS => 404,
      ErrorObject::TITLE => 'Invalid Relationship',
      ErrorObject::DETAIL => 'The resource for this relationship cannot be found.',
    ],
  ];

  protected $_types = [];
  protected $_allowEmpty = true;
  protected $_callback;

  public function setTypes($typeOrTypes)
  {
    $this->_types = is_array($typeOrTypes) ? $typeOrTypes : [$typeOrTypes];

    return $this;
  }

  public function isType($type)
  {
    return in_array($type, $this->_types, true);
  }

  public function setAllowEmpty($allow)
  {
    $this->_allowEmpty = (bool) $allow;

    return $this;
  }

  public function isEmptyAllowed()
  {
    return (bool) $this->_allowEmpty;
  }

  public function setCallback($callback)
  {
    if (!is_callable($callback)) {
      throw new \InvalidArgumentException('Expecting a valid callback.');
    }

    $this->_callback = $callback;

    return $this;
  }

  public function getCallback()
  {
    if (!$this->hasCallback()) {
      throw new \RuntimeException('No callback set.');
    }

    return $this->_callback;
  }

  public function hasCallback()
  {
    return is_callable($this->_callback);
  }

  protected function validate($value)
  {
    // must be an object.
    if (!is_object($value)) {
      $this->error(static::ERROR_INVALID_VALUE);
      return;
    }

    $object = new Relationship($value);

    // must be a has many relationship
    if (!$object->isHasMany()) {
      $this->error(static::ERROR_INVALID_VALUE)
        ->source()
        ->setPointer('/' . Relationship::DATA);
      return;
    }

    /** @var ResourceIdentifierCollection $data */
    $data = $object->getData();

    // if empty, empty relationship must be allowed.
    if ($data->isEmpty() && !$this->isEmptyAllowed()) {
      $this->error(static::ERROR_EMPTY_DISALLOWED)
        ->source()
        ->setPointer('/' . Relationship::DATA);
      return;
    } elseif ($data->isEmpty()) {
      return;
    }

    // check that each resource identifier is valid.
    foreach ($data as $key => $identifier) {
      $this->validateIdentifier($identifier, $key);
    }

    if ($this->hasCallback()) {
      $this->validateCallback($data);
    }
  }

  protected function validateIdentifier(ResourceIdentifier $identifier, $index)
  {
    $pointer = sprintf('/%s/%s/', Relationship::DATA, $index);

    // type must be acceptable
    if (!$identifier->hasType() || !$this->isType($identifier->getType())) {
      $this->error(static::ERROR_INVALID_TYPE)
        ->source()
        ->setPointer($pointer . ResourceIdentifier::TYPE);
    }

    $id = $identifier->hasId() ? $identifier->getId() : null;

    // id must be set an be either a non-empty string or an integer.
    if ((!is_string($id) && !is_int($id)) || (is_string($id) && empty($id))) {
      $this->error(static::ERROR_INVALID_ID)
        ->source()
        ->setPointer($pointer . ResourceIdentifier::ID);
    }
  }

  protected function validateCallback(ResourceIdentifierCollection $collection)
  {
    $check = call_user_func($this->getCallback(), $collection);
    $pointer = '/' . Relationship::DATA;

    if (!is_array($check) && false == $check) {
      $this->error(static::ERROR_INVALID_COLLECTION)
        ->source()
        ->setPointer($pointer);
    }

    if (!is_array($check)) {
      return;
    }

    $count = count($collection);

    foreach ($check as $index) {

      if (!is_numeric($index) || 0 > $index || $count <= $index) {
        throw new \RuntimeException('Invalid error index.');
      }

      $this->error(static::ERROR_NOT_FOUND)
        ->source()
        ->setPointer($pointer . '/' . $index);
    }
  }
}
