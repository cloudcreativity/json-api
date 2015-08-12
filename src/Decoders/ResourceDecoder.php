<?php

namespace CloudCreativity\JsonApi\Decoders;

use CloudCreativity\JsonApi\Error\MultiErrorException;
use CloudCreativity\JsonApi\Object\Resource\Resource;
use CloudCreativity\JsonApi\Validator\ValidatorAwareTrait;

class ResourceDecoder extends AbstractDecoder
{

    use ValidatorAwareTrait;

    /**
     * @param string $content
     * @return Resource
     * @throws MultiErrorException
     */
    public function decode($content)
    {
        $content = $this->parseJson($content);
        $validator = $this->getValidator();

        if (!$validator->isValid($content)) {
            throw new MultiErrorException($validator->getErrors(), 'Invalid request body content.');
        }

        return new Resource($content);
    }
}