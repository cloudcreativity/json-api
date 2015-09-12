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

namespace CloudCreativity\JsonApi\Exceptions;

use CloudCreativity\JsonApi\Contracts\Encoder\EncoderAwareInterface;
use CloudCreativity\JsonApi\Encoder\EncoderAwareTrait;
use Exception;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Exceptions\BaseRenderer;

/**
 * Class ErrorRenderer
 * @package CloudCreativity\JsonApi
 */
class ErrorRenderer extends BaseRenderer implements EncoderAwareInterface
{

    use EncoderAwareTrait;

    /**
     * @param Exception $e
     * @return string
     */
    public function getContent(Exception $e)
    {
        if (!$e instanceof ErrorInterface || !$this->hasEncoder()) {
            return null;
        }

        return $this
            ->getEncoder()
            ->encodeError($e);
    }

    /**
     * @param Exception $e
     * @return mixed
     */
    public function render(Exception $e)
    {
        if ($e instanceof ErrorInterface && !$this->getStatusCode() && $e->getStatus()) {
            $this->withStatusCode($e->getStatus());
        }

        return parent::render($e);
    }
}
