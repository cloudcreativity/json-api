<?php

namespace CloudCreativity\JsonApi\Validator\Resource;

use CloudCreativity\JsonApi\Validator\ResourceIdentifier\ExpectedIdValidator;

trait IdValidatorTrait
{

    /**
     * @var mixed
     */
    protected $_id;

    /**
     * @param $id
     * @return $this
     */
    protected function setId($id)
    {
        $this->_id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return ExpectedIdValidator
     */
    public function getIdValidator()
    {
        return new ExpectedIdValidator($this->getId());
    }
}