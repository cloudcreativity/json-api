<?php

namespace CloudCreativity\JsonApi\Config;

use CloudCreativity\JsonApi\Contracts\Config\CodecMatcherRepositoryInterface;
use CloudCreativity\JsonApi\Contracts\Config\EncodersRepositoryInterface;
use CloudCreativity\JsonApi\Decoders\DocumentDecoder;
use Neomerx\JsonApi\Codec\CodecMatcher;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Factories\FactoryInterface;
use Neomerx\JsonApi\Parameters\Headers\MediaType;

/**
 * Class CodecMatcherRepository
 * @package CloudCreativity\JsonApi
 */
class CodecMatcherRepository implements CodecMatcherRepositoryInterface
{

    /** Config key for a media type's encoder */
    const ENCODER = 'encoder';
    /** Config key for a media type's decoder */
    const DECODER = 'decoder';

    /**
     * @var FactoryInterface
     */
    private $_factory;

    /**
     * @var EncodersRepositoryInterface
     */
    private $_encoders;

    /**
     * @var array
     */
    private $_config = [];

    /**
     * @param FactoryInterface $factory
     * @param EncodersRepositoryInterface $encoders
     */
    public function __construct(
        FactoryInterface $factory,
        EncodersRepositoryInterface $encoders
    ) {
        $this->_factory = $factory;
        $this->_encoders = $encoders;
    }

    /**
     * @param $name
     *      the name of the codec matcher.
     * @param array $extras
     *      runtime extras to use for the encoder.
     * @return CodecMatcherInterface
     */
    public function get($name = self::DEFAULTS, array $extras = [])
    {
        $name ?: static::DEFAULTS;
        $config = (static::DEFAULTS === $name) ? $this->defaults() : $this->merge($name);
        $matcher = new CodecMatcher();

        foreach ($this->parse($config, $extras) as $mediaType => list($encoder, $decoder)) {

            $mediaType = MediaType::parse(0, $mediaType);

            if ($encoder) {
                $matcher->registerEncoder($mediaType, $encoder);
            }

            if ($decoder) {
                $matcher->registerDecoder($mediaType, $decoder);
            }
        }

        return $matcher;
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
     * @param $key
     * @return array
     */
    protected function merge($key)
    {
        return array_merge($this->defaults(), $this->find($key));
    }

    /**
     * @param array $config
     * @param array $extras
     * @return \Generator
     */
    protected function parse(array $config, array $extras = [])
    {
        foreach ($config as $mediaType => $values) {

            $encoder = isset($values[static::ENCODER]) ? $values[static::ENCODER] : null;
            $decoder = isset($values[static::DECODER]) ? $values[static::DECODER] : null;

            yield $mediaType => [$this->encoder($encoder, $extras), $this->decoder($decoder)];
        }
    }

    /**
     * @param $name
     * @param array $extras
     * @return \Closure|null
     */
    protected function encoder($name, array $extras)
    {
        if (!$name) {
            return null;
        }

        $repository = $this->_encoders;

        return function () use ($repository, $name, $extras) {
            return $repository->get($name, $extras);
        };
    }

    /**
     * @param $name
     * @return \Closure|null
     */
    protected function decoder($name)
    {
        if (!$name) {
            return null;
        }

        /** @todo use a DecoderRepository */
        return function () {
            return new DocumentDecoder();
        };
    }

}
