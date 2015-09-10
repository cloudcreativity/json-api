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

namespace CloudCreativity\JsonApi\Codec;

use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;

/**
 * Class CodecMatcherAwareTrait
 * @package CloudCreativity\JsonApi
 */
trait CodecMatcherAwareTrait
{

    /**
     * @var CodecMatcherInterface|null
     */
    private $codecMatcher;

    /**
     * @param CodecMatcherInterface $matcher
     * @return $this
     */
    public function setCodecMatcher(CodecMatcherInterface $matcher)
    {
        $this->codecMatcher = $matcher;

        return $this;
    }

    /**
     * @return CodecMatcherInterface
     */
    public function getCodecMatcher()
    {
        if (!$this->codecMatcher instanceof CodecMatcherInterface) {
            $this->codecMatcher = new CodecMatcher();
        }

        return $this->codecMatcher;
    }
}
