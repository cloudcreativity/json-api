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

namespace CloudCreativity\JsonApi\Helpers;

use CloudCreativity\JsonApi\Object\Document\Document;
use Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;
use Neomerx\JsonApi\Contracts\Integration\CurrentRequestInterface;
use Neomerx\JsonApi\Contracts\Parameters\ParametersInterface;

/**
 * Class DocumentProviderTrait
 * @package CloudCreativity\JsonApi
 */
trait DocumentProviderTrait
{

    /**
     * @return CodecMatcherInterface
     */
    abstract public function getCodecMatcher();

    /**
     * @return ParametersInterface
     */
    abstract public function getParameters();

    /**
     * @return CurrentRequestInterface
     */
    abstract public function getCurrentRequest();

    /**
     * @return Document
     */
    public function getDocument()
    {
        $matcher = $this->getCodecMatcher();

        if (!$matcher->getDecoder() === null) {
            $matcher->findDecoder($this->getParameters()->getContentTypeHeader());
        }

        $decoder = $matcher->getDecoder();

        if (!$decoder instanceof DecoderInterface) {
            throw new \RuntimeException('No decoder instance available.');
        }

        $content = $decoder->decode($this->getCurrentRequest()->getContent());

        if (is_object($content) && !$content instanceof Document) {
            $content = new Document($content);
        }

        if ($content instanceof Document) {
            throw new \RuntimeException('Expecting decoding to return an object or Document object.');
        }

        return $content;
    }
}
