<?php

namespace CloudCreativity\JsonApi\Contracts\Config;

use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;

/**
 * Interface EncodersRepositoryInterface
 * @package CloudCreativity\JsonApi
 */
interface EncodersRepositoryInterface extends ConfigurableInterface
{

    /**
     * @param string $schemas
     *      the name of the schemas to use.
     * @param string $encoderOptions
     *      the name of the encoder options to use.
     * @param array $extras
     *      extra runtime configuration to add to encoder options.
     * @return EncoderInterface
     */
    public function get(
        $schemas = SchemasRepositoryInterface::DEFAULTS,
        $encoderOptions = EncoderOptionsRepositoryInterface::DEFAULTS,
        array $extras = []
    );
}
