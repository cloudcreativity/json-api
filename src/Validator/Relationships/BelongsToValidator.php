<?php

namespace CloudCreativity\JsonApi\Validator\Relationships;

use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;
use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Object\Relationships\Relationship;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;
use CloudCreativity\JsonApi\Validator\AbstractValidator;

class BelongsToValidator extends AbstractValidator implements ConfigurableInterface
{

    // Config constants
    const TYPES = 'types';
    const TYPE = self::TYPES;
    const ALLOW_EMPTY = 'allowEmpty';
    const CALLBACK = 'callback';

    // Error constants
    const ERROR_INVALID_VALUE = 'invalid-value';
    const ERROR_INVALID_TYPE = 'invalid-resource-type';
    const ERROR_INVALID_ID = 'invalid-resouce-id';
    const ERROR_NULL_DISALLOWED = 'relationship-required';
    const ERROR_NOT_FOUND = 'not-found';

    /**
     * @var array
     */
    protected $_types = [];

    /**
     * @var bool
     */
    protected $_allowEmpty = true;

    /**
     * @var callable|null
     */
    protected $_callback;

    /**
     * @var array
     */
    protected $templates = [
        self::ERROR_INVALID_VALUE => [
            ErrorObject::CODE => self::ERROR_INVALID_VALUE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Value',
            ErrorObject::DETAIL => 'Value provided is invalid for a belongs-to relationship.',
        ],
        self::ERROR_INVALID_TYPE => [
            ErrorObject::CODE => self::ERROR_INVALID_TYPE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Relationship',
            ErrorObject::DETAIL => 'This belongs-to relationship does not accept the specified resource object type.',
        ],
        self::ERROR_INVALID_ID => [
            ErrorObject::CODE => self::ERROR_INVALID_ID,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Relationship',
            ErrorObject::DETAIL => 'The supplied belongs-to relationship id is missing or invalid.',
        ],
        self::ERROR_NULL_DISALLOWED => [
            ErrorObject::CODE => self::ERROR_NULL_DISALLOWED,
            ErrorObject::STATUS => 422,
            ErrorObject::TITLE => 'Invalid Relationship',
            ErrorObject::DETAIL => 'This relationship cannot be set to an empty value.',
        ],
        self::ERROR_NOT_FOUND => [
            ErrorObject::CODE => self::ERROR_NOT_FOUND,
            ErrorObject::STATUS => 404,
            ErrorObject::TITLE => 'Invalid Relationship',
            ErrorObject::DETAIL => 'The resource for this relationship cannot be found.',
        ],
    ];

    /**
     * @param $typeOrTypes
     */
    public function __construct($typeOrTypes = null)
    {
        if (!is_null($typeOrTypes)) {
            $this->setTypes($typeOrTypes);
        }
    }

    /**
     * @param $typeOrTypes
     * @return $this
     */
    public function setTypes($typeOrTypes)
    {
        $this->_types = is_array($typeOrTypes) ? $typeOrTypes : [$typeOrTypes];

        return $this;
    }

    /**
     * @param $type
     * @return bool
     */
    public function isType($type)
    {
        return in_array($type, $this->_types, true);
    }

    /**
     * @param $bool
     * @return $this
     */
    public function setAllowEmpty($bool)
    {
        $this->_allowEmpty = (bool) $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmptyAllowed()
    {
        return (bool) $this->_allowEmpty;
    }

    /**
     * @param $callback
     * @return $this
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Expecting a valid callback.');
        }

        $this->_callback = $callback;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCallback()
    {
        if (!$this->hasCallback()) {
            throw new \RuntimeException('No callback set.');
        }

        return $this->_callback;
    }

    /**
     * @return bool
     */
    public function hasCallback()
    {
        return is_callable($this->_callback);
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        if (isset($config[static::TYPES])) {
            $this->setTypes($config[static::TYPES]);
        }

        if (array_key_exists(static::ALLOW_EMPTY, $config)) {
            $this->setAllowEmpty($config[static::ALLOW_EMPTY]);
        }

        if (isset($config[static::CALLBACK])) {
            $this->setCallback($config[static::CALLBACK]);
        }

        return $this;
    }

    /**
     * @param $value
     */
    protected function validate($value)
    {
        // must be an object
        if (!is_object($value)) {
            $this->error(static::ERROR_INVALID_VALUE);
            return;
        }

        $object = new Relationship($value);

        // must be a belongs to relationship
        if (!$object->isBelongsTo()) {
            $this->error(static::ERROR_INVALID_VALUE)
                ->source()
                ->setPointer('/' . Relationship::DATA);
            return;
        }

        $data = $object->getData();

        // must not be empty if empty is not allowed.
        if (!$data && !$this->isEmptyAllowed()) {
            $this->error(static::ERROR_NULL_DISALLOWED)
                ->source()
                ->setPointer('/' . Relationship::DATA);
        }

        // if empty, is valid at this point so return.
        if (!$data) {
            return;
        }

        // type must be acceptable
        if (!$data->hasType() || !$this->isType($data->getType())) {
            $this->error(static::ERROR_INVALID_TYPE)
                ->source()
                ->setPointer('/' . Relationship::DATA . '/' . ResourceIdentifier::TYPE);
        }

        $id = $data->hasId() ? $data->getId() : null;

        // id must be set an be either a non-empty string or an integer.
        if ((!is_string($id) && !is_int($id)) || (is_string($id) && empty($id))) {
            $this->error(static::ERROR_INVALID_ID)
                ->source()
                ->setPointer('/' . Relationship::DATA . '/' . ResourceIdentifier::ID);
        }

        // check the callback, if one exists.
        if ($this->hasCallback() && false == call_user_func($this->getCallback(), $data)) {
            $this->error(static::ERROR_NOT_FOUND)
                ->source()
                ->setPointer('/' . Relationship::DATA);
        }
    }
}
