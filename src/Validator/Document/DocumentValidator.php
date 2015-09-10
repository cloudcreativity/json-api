<?php

/**
 * Copyright 2015 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CloudCreativity\JsonApi\Validator\Document;

use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Object\Document\Document;
use CloudCreativity\JsonApi\Object\StandardObject;
use CloudCreativity\JsonApi\Validator\AbstractValidator;

/**
 * Class DocumentValidator
 * @package CloudCreativity\JsonApi
 */
class DocumentValidator extends AbstractValidator
{

    // Error constants
    const ERROR_INVALID_VALUE = 'invalid-value';
    const ERROR_MISSING_DATA = 'missing-data';

    /**
     * @var array
     */
    protected $templates = [
        self::ERROR_INVALID_VALUE => [
            ErrorObject::CODE => self::ERROR_INVALID_VALUE,
            ErrorObject::TITLE => 'Invalid Value',
            ErrorObject::DETAIL => 'Request document must be an object.',
            ErrorObject::STATUS => 400,
        ],
        self::ERROR_MISSING_DATA => [
            ErrorObject::CODE => self::ERROR_MISSING_DATA,
            ErrorObject::TITLE => 'Missing Data',
            ErrorObject::DETAIL => 'Request document object must have a data member.',
            ErrorObject::STATUS => 400,
        ],
    ];

    /**
     * @var ValidatorInterface|null
     */
    private $dataValidator;

    /**
     * @param ValidatorInterface|null $dataValidator
     */
    public function __construct(ValidatorInterface $dataValidator = null)
    {
        if ($dataValidator) {
            $this->setDataValidator($dataValidator);
        }
    }

    /**
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function setDataValidator(ValidatorInterface $validator)
    {
        $this->dataValidator = $validator;

        return $this;
    }

    /**
     * @return ValidatorInterface
     */
    public function getDataValidator()
    {
        if (!$this->dataValidator instanceof ValidatorInterface) {
            throw new \RuntimeException('No data validator set.');
        }

        return $this->dataValidator;
    }

    /**
     * @return bool
     */
    public function hasDataValidator()
    {
        return $this->dataValidator instanceof ValidatorInterface;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return true;
    }

    /**
     * @param $value
     */
    protected function validate($value)
    {
        if (!is_object($value)) {
            $this->error(static::ERROR_INVALID_VALUE);
            return;
        }

        $object = new StandardObject($value);

        if (!$object->has(Document::DATA)) {
            $this->error(static::ERROR_MISSING_DATA);
            return;
        }

        if ($this->hasDataValidator()) {
            $this->validateData($object->get(Document::DATA));
        }
    }

    /**
     * @param $data
     * @return $this
     */
    protected function validateData($data)
    {
        $validator = $this->getDataValidator();

        if (!$validator->isValid($data)) {
            $this->getErrors()
                ->merge($validator
                    ->getErrors()
                    ->setSourcePointer(function ($current) {
                        return '/data' . $current;
                    }));
        }

        return $this;
    }
}
