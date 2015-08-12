<?php

namespace CloudCreativity\JsonApi\Validator\Resource;

use CloudCreativity\JsonApi\Validator\ResourceIdentifier\ExpectedTypeValidator;

trait TypeValidatorTrait
{

  /**
   * @var mixed
   */
  protected $_type;

  /**
   * @param $type
   * @return $this
   */
  public function setType($type)
  {
    $this->_type = $type;

    return $this;
  }

  /**
   * @return string
   */
  public function getType()
  {
    return (string) $this->_type;
  }

  /**
   * @return ExpectedTypeValidator
   */
  public function getTypeValidator()
  {
    return new ExpectedTypeValidator($this->getType());
  }
}