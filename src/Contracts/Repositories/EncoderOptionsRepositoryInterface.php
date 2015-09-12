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

use Neomerx\JsonApi\Encoder\EncoderOptions;

/**
 * Interface EncoderRepositoryInterface
 * @package CloudCreativity\JsonApi
 */
interface EncoderOptionsRepositoryInterface extends MutableRepositoryInterface
{

    /** Options config key and its default setting. */
    const OPTIONS = 'options';
    const OPTIONS_DEFAULT = 0;

    /** Url config key and its default setting. */
    const URL_PREFIX = 'url-prefix';
    const URL_PREFIX_DEFAULT = null;

    /** Depth config key and its default setting */
    const DEPTH = 'depth';
    const DEPTH_DEFAULT = 512;

    /**
     * @param string|null $name
     *      the name of the encoder options set, or empty to get the default encoder options
     * @return EncoderOptions
     */
    public function getEncoderOptions($name = null);
}
