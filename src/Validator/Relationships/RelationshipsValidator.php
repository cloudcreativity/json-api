<?php

namespace CloudCreativity\JsonApi\Validator\Relationships;

use CloudCreativity\JsonApi\Validator\AbstractValidator;
use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Error\ErrorObject;

class RelationshipsValidator extends AbstractValidator
{

  const ERROR_INVALID_VALUE = 'invalid-value';
  const ERROR_UNRECOGNISED_RELATIONSHIP = 'not-recognised';
  const ERROR_REQUIRED_RELATIONSHIP = 'required';

  protected $templates = [
    self::ERROR_INVALID_VALUE => [
      ErrorObject::CODE => self::ERROR_INVALID_VALUE,
      ErrorObject::STATUS => 400,
      ErrorObject::TITLE => 'Invalid Value',
      ErrorObject::DETAIL => 'Invald relationships object value.',
    ],
  ];

  protected $_validators = [];
  protected $_required;

  public function setRequired(array $keys)
  {

  }

  public function getRequired()
  {

  }

  public function setValidators(array $validators)
  {
    foreach ($validators as $key => $validator) {

      if (!$validator instanceof ValidatorInterface) {
        throw new \InvalidArgumentException('Expecting array to only contain ValidatorInterface objects.');
      }

      $this->setValidator($key, $validator);
    }

    return $this;
  }

  public function setValidator($key, ValidatorInterface $validator)
  {
    $this->_validators[$key] = $validator;

    return $this;
  }

  public function getValidator($key)
  {
    if (!$this->hasValidator($key)) {
      throw new \RuntimeException(sprint('No validator set for "%s".', $key));
    }

    return $this->_validators[$key];
  }

  public function hasValidator($key)
  {
    return isset($this->_validators[$key]);
  }

  protected function validate($value)
  {
    if (!is_object($value)) {
      $this->error(static::ERROR_INVALID_VALUE);
      return;
    }

    foreach (get_object_vars($value) as $key => $value) {

      $validator = $this->getValidator($key);

      if ($validator->isValid($value)) {
        continue;
      }

      $this->getErrors()
        ->merge($validator
          ->getErrors()
          ->setSourcePointer(function ($current) use ($key) {
            return '/' . $key . $current;
          }));
    }
  }
}
