<?php

namespace CloudCreativity\JsonApi\Validator\Resource;

class UpdateResourceValidator extends AbstractResourceValidator
{

  use TypeValidatorTrait,
    IdValidatorTrait,
    AttributesValidatorTrait,
    RelationshipsValidatorTrait;
}