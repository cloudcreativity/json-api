<?php

namespace CloudCreativity\JsonApi\Decoders;

use CloudCreativity\JsonApi\Error\MultiErrorException;
use CloudCreativity\JsonApi\Object\Relationships\Relationship;
use CloudCreativity\JsonApi\Validator\ValidatorAwareTrait;

class RelationshipDecoder extends AbstractDecoder
{

    use ValidatorAwareTrait;

    /**
     * @param string $content
     * @return Relationship
     * @throws MultiErrorException
     */
    public function decode($content)
    {
        $content = $this->parseJson($content);
        $validator = $this->getValidator();

        if (!$validator->isValid($content)) {
            throw new MultiErrorException($validator->getErrors(), 'Invalid request body content.');
        }

        return new Relationship($content);
    }
}