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

use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;

/**
 * Interface DecodersRepository
 * @package CloudCreativity\JsonApi
 */
interface DecodersRepositoryInterface extends ConfigRepositoryInterface
{

    /** The type (fqn) of the decoder. */
    const TYPE = 'type';
    /** Options to set on the decoder. */
    const OPTIONS = 'options';

    /**
     * @param string|null $name
     *      get the named decoder or the default decoder if empty.
     * @return DecoderInterface
     */
    public function getDecoder($name = null);
}
