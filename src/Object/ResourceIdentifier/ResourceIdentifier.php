<?php

namespace CloudCreativity\JsonApi\Object\ResourceIdentifier;

use CloudCreativity\JsonApi\Object\StandardObject;

class ResourceIdentifier extends StandardObject
{

    const TYPE = 'type';
    const ID = 'id';
    const META = 'meta';

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->set(static::TYPE, $type);

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        if (!$this->hasType()) {
            throw new \RuntimeException('No type set.');
        }

        return $this->get(static::TYPE);
    }

    /**
     * @return bool
     */
    public function hasType()
    {
        return $this->has(static::TYPE);
    }

    /**
     * @param $typeOrTypes
     * @return bool
     */
    public function isType($typeOrTypes)
    {
        $types = is_array($typeOrTypes) ? $typeOrTypes : [$typeOrTypes];
        $type = $this->get(static::TYPE);

        foreach ($types as $check) {

            if ($type === $check) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $map
     * @return mixed
     */
    public function mapType(array $map)
    {
        $type = $this->getType();

        if (array_key_exists($type, $map)) {
            return $map[$type];
        }

        throw new \RuntimeException(sprintf('Type "%s" is not in the supplied map.', $type));
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->set(static::ID, $id);

        return $this;
    }

    /**
     * @return string|int
     */
    public function getId()
    {
        if (!$this->hasId()) {
            throw new \RuntimeException('No id set.');
        }

        return $this->get(static::ID);
    }

    /**
     * @return bool
     */
    public function hasId()
    {
        return $this->has(static::ID);
    }

    /**
     * @return bool
     */
    public function isComplete()
    {
        return $this->hasType() && $this->hasId();
    }

    /**
     * @return StandardObject
     */
    public function getMeta()
    {
        return new StandardObject($this->get(static::META));
    }

}
