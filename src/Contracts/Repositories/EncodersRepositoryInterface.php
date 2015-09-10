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

namespace CloudCreativity\JsonApi\Contracts\Repositories;

use CloudCreativity\JsonApi\Contracts\Stdlib\ConfigurableInterface;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;

/**
 * Interface EncodersRepositoryInterface
 * @package CloudCreativity\JsonApi
 */
interface EncodersRepositoryInterface extends ConfigurableInterface
{

    /** Config key for schemas configuration */
    const SCHEMAS = 'schemas';

    /** Config key for encoder options cnofiguration */
    const OPTIONS = 'options';

    /**
     * @param string|null $schemas
     *      the named schema set to use, or empty to use the default schemas.
     * @param null $options
     *      the named encoder options to use, or empty to use the default options.
     * @return EncoderInterface
     */
    public function getEncoder($schemas = null, $options = null);
}
