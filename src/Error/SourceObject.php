<?php

namespace CloudCreativity\JsonApi\Error;

use CloudCreativity\JsonApi\Object\StandardObject;

class SourceObject extends StandardObject implements \JsonSerializable
{

    const POINTER = 'pointer';
    const PARAMETER = 'parameter';

    /**
     * @param $pointer
     * @return $this
     */
    public function setPointer($pointer)
    {
        if ($pointer instanceof \Closure) {
            $pointer = $pointer($this->getPointer());
        }

        $this->set(static::POINTER, $pointer);

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPointer()
    {
        return $this->get(static::POINTER, null);
    }

    /**
     * @param $parameter
     * @return $this
     */
    public function setParameter($parameter)
    {
        $this->set(static::PARAMETER, $parameter);

        return $this;
    }

    /**
     * @return null|string
     */
    public function getParameter()
    {
        return $this->get(static::PARAMETER, null);
    }

    /**
     * @return object
     */
    public function jsonSerialize()
    {
        return $this->getProxy();
    }
}
