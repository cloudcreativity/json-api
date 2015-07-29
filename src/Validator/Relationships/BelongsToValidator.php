<?php

namespace CloudCreativity\JsonApi\Validator\Relationships;

use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Object\Relationships\Relationship;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;
use CloudCreativity\JsonApi\Validator\AbstractValidator;

class BelongsToValidator extends AbstractValidator
{

  const ERROR_INVALID_VALUE = 'invalid-value';
  const ERROR_INVALID_TYPE = 'invalid-resource-type';
  const ERROR_INVALID_ID = 'invalid-resouce-id';
  const ERROR_NULL_DISALLOWED = 'relationship-required';

  protected $_types = [];
  protected $_allowEmpty = true;

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
      ErrorObject::DETAIL => 'This belongs-to relationship does not accept the specified resource object type.',
    ],
    self::ERROR_INVALID_ID => [
      ErrorObject::CODE => self::ERROR_INVALID_ID,
      ErrorObject::STATUS => 400,
      ErrorObject::TITLE => 'Invalid Relationship',
      ErrorObject::DETAIL => 'The supplied belongs-to relationship id is missing or invalid.',
    ],
    self::ERROR_NULL_DISALLOWED => [
      ErrorObject::CODE => self::ERROR_NULL_DISALLOWED,
      ErrorObject::STATUS => 422,
      ErrorObject::TITLE => 'Invalid Relationship',
      ErrorObject::DETAIL => 'This relationship cannot be set to an empty value.',
    ],
  ];

  public function setTypes($typeOrTypes)
  {
    $this->_types = is_array($typeOrTypes) ? $typeOrTypes : [$typeOrTypes];

    return $this;
  }

  public function isType($type)
  {
    return in_array($type, $this->_types, true);
  }

  public function setAllowEmpty($bool)
  {
    $this->_allowEmpty = (bool) $bool;

    return $this;
  }

  public function isEmptyAllowed()
  {
    return (bool) $this->_allowEmpty;
  }

  protected function validate($value)
  {
    // must be an object
    if (!is_object($value)) {
      $this->error(static::ERROR_INVALID_VALUE);
      return;
    }

    $object = new Relationship($value);

    // must be a belongs to relationship
    if (!$object->isBelongsTo()) {
      $this->error(static::ERROR_INVALID_VALUE)
        ->source()
        ->setPointer('/' . Relationship::DATA);
      return;
    }

    $data = $object->getData();

    // must not be empty if empty is not allowed.
    if (!$data && !$this->isEmptyAllowed()) {
      $this->error(static::ERROR_NULL_DISALLOWED)
        ->source()
        ->setPointer('/' . Relationship::DATA);
    }

    // if empty, is valid at this point so return.
    if (!$data) {
      return;
    }

    // type must be acceptable
    if (!$data->hasType() || !$this->isType($data->getType())) {
      $this->error(static::ERROR_INVALID_TYPE)
        ->source()
        ->setPointer('/' . Relationship::DATA . '/' . ResourceIdentifier::TYPE);
    }

    $id = $data->hasId() ? $data->getId() : null;

    // id must be set an be either a non-empty string or an integer.
    if ((!is_string($id) && !is_int($id)) || (is_string($id) && empty($id))) {
      $this->error(static::ERROR_INVALID_ID)
        ->source()
        ->setPointer('/' . Relationship::DATA . '/' . ResourceIdentifier::ID);
    }
  }
}
