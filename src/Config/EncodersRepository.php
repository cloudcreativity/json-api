<?php

namespace CloudCreativity\JsonApi\Config;

use CloudCreativity\JsonApi\Contracts\Config\EncoderOptionsRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Config\EncodersRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Config\SchemasRepositoryInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Factories\FactoryInterface;
use Neomerx\JsonApi\Encoder\Encoder;

/**
 * Class EncodersRepository
 * @package CloudCreativity\JsonApi
 */
class EncodersRepository implements EncodersRepositoryInterface
{

    /** Config key for schemas configuration */
    const SCHEMAS = 'schemas';

    /** Config key for encoder options configuration */
    const ENCODER_OPTIONS = 'encoder-options';

    /**
     * @var FactoryInterface
     */
    private $_factory;

    /**
     * @var SchemasRepositoryInterface
     */
    private $_schemas;

    /**
     * @var EncoderOptionsRepositoryInterface
     */
    private $_encoderOptions;

    /**
     * @param FactoryInterface $factory
     * @param SchemasRepositoryInterface $schemas
     * @param EncoderOptionsRepositoryInterface $encoderOptions
     */
    public function __construct(
        FactoryInterface $factory,
        SchemasRepositoryInterface $schemas,
        EncoderOptionsRepositoryInterface $encoderOptions
    ) {
        $this->_factory = $factory;
        $this->_schemas = $schemas;
        $this->_encoderOptions = $encoderOptions;
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory()
    {
        return $this->_factory;
    }

    /**
     * @param $name
     * @return array
     */
    public function getSchemas($name)
    {
        return $this->_schemas->get($name);
    }

    /**
     * @param $name
     * @param array $extras
     * @return \Neomerx\JsonApi\Encoder\EncoderOptions
     */
    public function getEncoderOptions($name, array $extras)
    {
        return $this->_encoderOptions->get($name, $extras);
    }

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
    ) {
        $schemas ?: SchemasRepositoryInterface::DEFAULTS;
        $encoderOptions ?: EncoderOptionsRepositoryInterface::DEFAULTS;

        return new Encoder(
            $this->getFactory(),
            $this->getSchemas($schemas),
            $this->getEncoderOptions($encoderOptions, $extras)
        );
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        if (isset($config[static::SCHEMAS]) && is_array($config[static::SCHEMAS])) {
            $this->_schemas->configure($config[static::SCHEMAS]);
        }

        if (isset($config[static::ENCODER_OPTIONS]) && is_array($config[static::ENCODER_OPTIONS])) {
            $this->_encoderOptions->configure($config[static::ENCODER_OPTIONS]);
        }

        return $this;
    }

}
