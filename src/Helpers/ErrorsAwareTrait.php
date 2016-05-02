<?php

namespace CloudCreativity\JsonApi\Helpers;

use Neomerx\JsonApi\Document\Error;
use Neomerx\JsonApi\Exceptions\ErrorCollection;

trait ErrorsAwareTrait
{

    /**
     * @var ErrorCollection|null
     */
    private $errors;

    /**
     * @return ErrorCollection
     */
    public function errors()
    {
        if (!$this->errors instanceof ErrorCollection) {
            $this->errors = new ErrorCollection();
        }

        return $this->errors;
    }

    /**
     * @param ErrorCollection $errors
     * @return $this
     */
    public function addErrors(ErrorCollection $errors)
    {
        /** @var Error $error */
        foreach ($errors as $error) {
            $this->errors()->add($error);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function reset()
    {
        $this->errors = null;

        return $this;
    }
}
