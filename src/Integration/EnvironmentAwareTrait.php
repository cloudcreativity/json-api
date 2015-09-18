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

namespace CloudCreativity\JsonApi\Integration;

use CloudCreativity\JsonApi\Contracts\Integration\EnvironmentInterface;
use RuntimeException;

/**
 * Class EnvironmentAwareTrait
 * @package CloudCreativity\JsonApi
 */
trait EnvironmentAwareTrait
{

    /**
     * @var EnvironmentInterface|null
     */
    private $environment;

    /**
     * @param EnvironmentInterface $environment
     * @return $this
     */
    public function setEnvironment(EnvironmentInterface $environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @return EnvironmentInterface
     */
    public function getEnvironment()
    {
        if (!$this->environment instanceof EnvironmentInterface) {
            throw new RuntimeException(sprintf('%s expects to be injected with a %s instance.', static::class, EnvironmentInterface::class));
        }

        return $this->environment;
    }
}
