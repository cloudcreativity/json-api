<?php

namespace CloudCreativity\JsonApi\Contracts\Integration;

use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Parameters\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Contracts\Parameters\ParametersInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;

/**
 * Interface EnvironmentServiceInterface
 * @package CloudCreativity\JsonApi
 */
interface EnvironmentInterface
{

    /**
     * Get the url prefix for links.
     *
     * @return string
     */
    public function getUrlPrefix();

    /**
     * Get the schemas for the current request.
     *
     * @return ContainerInterface
     */
    public function getSchemas();

    /**
     * @return bool
     */
    public function hasSchemas();

    /**
     * @return EncoderInterface
     */
    public function getEncoder();

    /**
     * @return bool
     */
    public function hasEncoder();

    /**
     * @return MediaTypeInterface
     */
    public function getEncoderMediaType();

    /**
     * @return DecoderInterface
     */
    public function getDecoder();

    /**
     * @return bool
     */
    public function hasDecoder();

    /**
     * @return MediaTypeInterface
     */
    public function getDecoderMediaType();

    /**
     * @return ParametersInterface
     */
    public function getParameters();

    /**
     * @return bool
     */
    public function hasParameters();
}
