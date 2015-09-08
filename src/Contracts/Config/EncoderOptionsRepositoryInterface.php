<?php

namespace CloudCreativity\JsonApi\Contracts\Config;

use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;
use Neomerx\JsonApi\Encoder\EncoderOptions;

/**
 * Interface EncoderRepositoryInterface
 * @package CloudCreativity\JsonApi
 */
interface EncoderOptionsRepositoryInterface extends ConfigurableInterface
{

    /** The config key for the default encoder options configuration. */
    const DEFAULTS = 'defaults';

    /** Options config key and its default setting. */
    const OPTIONS = 'options';
    const OPTIONS_DEFAULT = 0;

    /** Url config key and its default setting. */
    const URL_PREFIX = 'url-prefix';
    const URL_PREFIX_DEFAULT = null;

    /** Is Show Version Info config key and its default setting. */
    const IS_SHOW_VERSION_INFO = 'is-show-version-info';
    const IS_SHOW_VERSION_INFO_DEFAULT = false;

    /** Version Meta config key and its default setting. */
    const VERSION_META = 'version-meta';
    const VERSION_META_DEFAULT = null;

    /** Depth config key and its default setting */
    const DEPTH = 'depth';
    const DEPTH_DEFAULT = 512;

    /**
     * @param string $name
     *      the name of the encoder options set.
     * @param array $extras
     *      any runtime options that need to be applied.
     * @return EncoderOptions
     */
    public function get($name = self::DEFAULTS, array $extras = []);
}
