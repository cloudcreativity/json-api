<?php

namespace CloudCreativity\JsonApi\Validator\Resource;

class CreateResourceValidator extends AbstractResourceValidator
{

    use TypeValidatorTrait,
        AttributesValidatorTrait,
        RelationshipsValidatorTrait;

    /**
     * @return null
     */
    public function getIdValidator()
    {
        return null;
    }

}