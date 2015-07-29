<?php

namespace CloudCreativity\JsonApi\Validator\Type;

trait NullableTrait
{

    protected $_acceptNull;

    /**
     * @param $accept
     * @return $this
     */
    public function setAcceptNull($accept)
    {
        $this->_acceptNull = (bool) $accept;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNullAllowed()
    {
        return (bool) $this->_acceptNull;
    }
}