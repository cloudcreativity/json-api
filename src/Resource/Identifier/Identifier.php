<?php

namespace Appativity\JsonApi\Resource\Identifier;

class Identifier
{

    const TYPE = 'type';
    const ID = 'id';

    /**
     * @var mixed
     */
    protected $_type;

    /**
     * @var mixed
     */
    protected $_id;

    public function __construct($type = null, $id = null)
    {
        $this->setType($type)
            ->setId($id);
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->_type = $type;

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

        return $this->_type;
    }

    /**
     * @return bool
     */
    public function hasType()
    {
        return is_string($this->_type) && !empty($this->_type);
    }

    /**
     * @param $typeOrTypes
     * @return bool
     */
    public function isType($typeOrTypes)
    {
        $types = is_array($typeOrTypes) ? $typeOrTypes : [$typeOrTypes];

        foreach ($types as $check) {

            if ($this->_type === $check) {
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
        $this->_id = $id;

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

        return $this->_id;
    }

    /**
     * @return bool
     */
    public function hasId()
    {
        return (is_string($this->_id) || is_int($this->_id)) && !empty($this->_id);
    }

    /**
     * @return bool
     */
    public function isComplete()
    {
        return $this->hasType() && $this->hasId();
    }

    /**
     * @param array $input
     * @return $this
     */
    public function exchangeArray(array $input)
    {
        if (array_key_exists(static::TYPE, $input)) {
            $this->setType($input[static::TYPE]);
        }

        if (array_key_exists(static::ID, $input)) {
            $this->setId($input[static::ID]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            static::TYPE => $this->hasType() ? $this->getType() : null,
            static::ID => $this->hasId() ? $this->getId() : null,
        ];
    }

    /**
     * @param array $input
     * @return Identifier
     */
    public static function create(array $input)
    {
        $identifier = new static();
        $identifier->exchangeArray($input);
        return $identifier;
    }
}
