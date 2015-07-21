<?php

namespace Appativity\JsonApi\Resource\Identifier;

class IdentifierCollection implements \IteratorAggregate, \Countable
{

    /**
     * @var array
     */
    protected $_stack = [];

    /**
     * @param array $identifiers
     */
    public function __construct(array $identifiers = [])
    {
        $this->addMany($identifiers);
    }

    /**
     * @param Identifier $identifier
     * @return $this
     */
    public function add(Identifier $identifier)
    {
        if (!$this->has($identifier)) {
            $this->_stack[] = $identifier;
        }

        return $this;
    }

    /**
     * @param Identifier $identifier
     * @return bool
     */
    public function has(Identifier $identifier)
    {
        return in_array($identifier, $this->_stack);
    }

    /**
     * @param array $identifiers
     * @return $this
     */
    public function addMany(array $identifiers)
    {
        foreach ($identifiers as $identifier) {

            if (!$identifier instanceof Identifier) {
                throw new \InvalidArgumentException('Expecting only Identifier objects.');
            }

            $this->add($identifier);
        }

        return $this;
    }

    /**
     * @param array $identifiers
     * @return $this
     */
    public function setAll(array $identifiers)
    {
        $this->clear()->addMany($identifiers);

        return $this;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->_stack;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->_stack = [];

        return $this;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getAll());
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->_stack);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->_stack);
    }

    /**
     * @return bool
     */
    public function isComplete()
    {
        /** @var Identifier $identifier */
        foreach ($this as $identifier) {

            if (!$identifier->isComplete()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $typeOrTypes
     * @return bool
     */
    public function isOnly($typeOrTypes)
    {
        /** @var Identifier $identifier */
        foreach ($this as $identifier) {

            if (!$identifier->isType($typeOrTypes)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function getIds()
    {
        $ret = [];

        /** @var Identifier $identifier */
        foreach ($this as $identifier) {
            $ret[] = $identifier->getId();
        }

        return $ret;
    }

    /**
     * @param array|null $typeMap
     * @return array
     */
    public function map(array $typeMap = null)
    {
        $ret = [];

        /** @var Identifier $identifier */
        foreach ($this as $identifier) {

            $key = is_array($typeMap) ? $identifier->mapType($typeMap) : $identifier->getType();

            if (!isset($ret[$key])) {
                $ret[$key] = [];
            }

            $ret[$key][] = $identifier->getId();
        }

        return $ret;
    }

    /**
     * @param array $input
     * @return $this
     */
    public function exchangeArray(array $input)
    {
        foreach ($input as $identifier) {

            if (is_array($identifier)) {
                $identifier = Identifier::create($identifier);
            }

            if ($identifier instanceof Identifier) {
                $this->add($identifier);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $ret = [];

        /** @var Identifier $identifier */
        foreach ($this as $identifier) {
            $ret[] = $identifier->toArray();
        }

        return $ret;
    }

    /**
     * @param array $input
     * @return static
     */
    public static function create(array $input)
    {
        $collection = new static();
        $collection->exchangeArray($input);
        return $collection;
    }
}
