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

use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;

/**
 * Interface CodecMatcherRepositoryInterface
 * @package CloudCreativity\JsonApi
 */
interface CodecMatcherRepositoryInterface extends MutableRepositoryInterface
{


    /** Config key for a codec matcher's encoders */
    const ENCODERS = 'encoders';
    /** Config key for named schemas to use for an encoder. */
    const ENCODER_SCHEMAS = 'schemas';
    /** Config key for named encoder options to use for an encoder. */
    const ENCODER_OPTIONS = 'options';
    /** Config key for a codec matcher's decoders */
    const DECODERS = 'decoders';

    /**
     * @param string|null $name
     *      the name of the codec matcher or empty to get the default codec matcher.
     * @return CodecMatcherInterface
     */
    public function getCodecMatcher($name = null);
}
