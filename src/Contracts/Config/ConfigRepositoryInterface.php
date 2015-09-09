<?php

/**
 * Copyright 2015 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CloudCreativity\JsonApi\Contracts\Config;

use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;

/**
 * Interface RepositoryInterface
 * @package CloudCreativity\JsonApi
 */
interface ConfigRepositoryInterface extends ConfigurableInterface
{

    /** Config key for default configuration */
    const DEFAULTS = 'defaults';

    /**
     * Provide a callback to apply runtime configuration options.
     *
     * The callback will be invoked after the repository has loaded the config, but before it is used (either to
     * generate an object or to return a config array). The provided callback will receive the following two
     * arguments:
     *
     * `function (MutableConfigInterface $config, string $name)`
     *
     * Where `$name` is the name of whatever has been loaded.
     *
     * @param \Closure $callback
     * @return $this
     */
    public function addModifier(\Closure $callback);
}
