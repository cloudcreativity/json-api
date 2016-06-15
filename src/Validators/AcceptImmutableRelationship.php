<?php

namespace CloudCreativity\JsonApi\Validators;

use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceInterface;
use CloudCreativity\JsonApi\Contracts\Validators\AcceptRelatedResourceInterface;
use CloudCreativity\JsonApi\Object\ResourceIdentifier;

class AcceptImmutableRelationship implements AcceptRelatedResourceInterface
{

    /**
     * @var ResourceIdentifier|null
     */
    private $current;

    /**
     * AcceptImmutableRelationship constructor.
     * @param string $type
     * @param string|int|null $id
     */
    public function __construct($type, $id = null)
    {
        if ($type && $id) {
            $this->current = ResourceIdentifier::create($type, $id);
        }
    }

    /**
     * @inheritdoc
     */
    public function accept(
        ResourceIdentifierInterface $identifier,
        $key = null,
        ResourceInterface $resource = null
    ) {
        if (!$this->current) {
            return true;
        }

        return $this->current->type() == $identifier->type() &&
            $this->current->id() == $identifier->id();
    }


}
