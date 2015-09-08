<?php

namespace CloudCreativity\JsonApi\Contracts\Config;

use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;

/**
 * Interface CodecMatcherRepositoryInterface
 * @package CloudCreativity\JsonApi
 */
interface CodecMatcherRepositoryInterface extends ConfigurableInterface
{

    /** Config key for the defaults */
    const DEFAULTS = 'defaults';

    /**
     * @param $name
     *      the name of the codec matcher.
     * @return CodecMatcherInterface
     */
    public function get($name = self::DEFAULTS);
}
