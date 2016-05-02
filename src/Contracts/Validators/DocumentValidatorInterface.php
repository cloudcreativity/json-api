<?php

namespace CloudCreativity\JsonApi\Contracts\Validators;

use CloudCreativity\JsonApi\Contracts\Stdlib\ErrorsAwareInterface;
use CloudCreativity\JsonApi\Contracts\Object\Document\DocumentInterface;

interface DocumentValidatorInterface extends ErrorsAwareInterface
{

    /**
     * @param DocumentInterface $document
     * @return bool
     */
    public function isValid(DocumentInterface $document);
}
