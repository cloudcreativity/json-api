<?php

namespace CloudCreativity\JsonApi\Contracts\Config;

use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;

/**
 * Interface SchemaRepositoryInterface
 * @package CloudCreativity\JsonApi
 */
interface SchemasRepositoryInterface extends ConfigurableInterface
{

    /** The name of the default set of schemas. */
    const DEFAULTS = 'defaults';

    /**
     * @param string $schemas
     *      The name of the schema set. If omitted, will return the default set.
     * @return array
     */
    public function get($schemas = self::DEFAULTS);
}
