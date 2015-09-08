<?php

namespace CloudCreativity\JsonApi\Config;

use CloudCreativity\JsonApi\Contracts\Config\SchemasRepositoryInterface;

/**
 * Class SchemasRepository
 * @package CloudCreativity\JsonApi
 */
class SchemasRepository implements SchemasRepositoryInterface
{

    /**
     * @var array
     */
    protected $_config;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->configure($config);
    }

    /**
     * @param string $name
     * @return array
     */
    public function get($name = self::DEFAULTS)
    {
        return (self::DEFAULTS === $name) ? $this->defaults() : $this->merge($name);
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        $this->_config = $config;

        return $this;
    }

    /**
     * @return array
     */
    protected function defaults()
    {
        return $this->find(static::DEFAULTS);
    }

    /**
     * @param $key
     * @return array
     */
    protected function find($key)
    {
        return array_key_exists($key, $this->_config) ? (array) $this->_config[$key] : [];
    }

    /**
     * @param $key
     * @return array
     */
    protected function merge($key)
    {
        return array_merge($this->defaults(), $this->find($key));
    }

}
