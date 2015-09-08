<?php

namespace CloudCreativity\JsonApi\Config;

use CloudCreativity\JsonApi\Contracts\Config\EncoderOptionsRepositoryInterface;
use Neomerx\JsonApi\Encoder\EncoderOptions;

/**
 * Class EncoderOptionsRepository
 * @package CloudCreativity\JsonApi
 */
class EncoderOptionsRepository implements EncoderOptionsRepositoryInterface
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
     * @param array $extras
     * @return EncoderOptions
     */
    public function get($name = self::DEFAULTS, array $extras = [])
    {
        $config = $this->config($name, $extras);

        $options = array_key_exists(static::OPTIONS, $config) ?
            $config[static::OPTIONS] : static::OPTIONS_DEFAULT;

        $urlPrefix = array_key_exists(static::URL_PREFIX, $config) ?
            $config[static::URL_PREFIX] : static::URL_PREFIX_DEFAULT;

        $isShowVersionInfo = array_key_exists(static::IS_SHOW_VERSION_INFO, $config) ?
            $config[static::IS_SHOW_VERSION_INFO] : static::IS_SHOW_VERSION_INFO_DEFAULT;

        $versionMeta = array_key_exists(static::VERSION_META, $config) ?
            $config[static::VERSION_META] : static::VERSION_META_DEFAULT;

        $depth = array_key_exists(static::DEPTH, $config) ?
            $config[static::DEPTH] : static::DEPTH_DEFAULT;

        return new EncoderOptions(
            $options,
            $urlPrefix,
            $isShowVersionInfo,
            $versionMeta,
            $depth
        );
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
     * @param $key
     * @param array $extras
     * @return array
     */
    protected function config($key, array $extras = [])
    {
        $set = (static::DEFAULTS === $key) ? $this->defaults() : $this->merge($key);

        return array_merge_recursive($extras, $set);
    }

    /**
     * @param $key
     * @return array
     */
    protected function merge($key)
    {
        return array_merge_recursive($this->defaults(), $this->find($key));
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
     * @return array
     */
    protected function defaults()
    {
        return $this->find(static::DEFAULTS);
    }

    /**
     * @return array
     */
    public static function defaultOptions()
    {
        return [
            static::OPTIONS => static::OPTIONS_DEFAULT,
            static::URL_PREFIX => static::URL_PREFIX_DEFAULT,
            static::IS_SHOW_VERSION_INFO => static::IS_SHOW_VERSION_INFO_DEFAULT,
            static::VERSION_META => static::VERSION_META,
            static::DEPTH => static::DEPTH,
        ];
    }
}
