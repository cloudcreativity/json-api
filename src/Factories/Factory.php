<?php

/**
 * Copyright 2017 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Factories;

use CloudCreativity\JsonApi\Contracts\Factories\FactoryInterface;
use CloudCreativity\JsonApi\Contracts\Http\ApiInterface;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use CloudCreativity\JsonApi\Encoder\Encoder;
use CloudCreativity\JsonApi\Http\Requests\RequestFactory;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Neomerx\JsonApi\Encoder\EncoderOptions;
use Neomerx\JsonApi\Factories\Factory as BaseFactory;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Factory
 *
 * @package CloudCreativity\JsonApi
 */
class Factory extends BaseFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param EncoderOptions|null $encoderOptions
     * @return Encoder
     */
    public function createEncoder(ContainerInterface $container, EncoderOptions $encoderOptions = null)
    {
        $encoder = new Encoder($this, $container, $encoderOptions);
        $encoder->setLogger($this->logger);

        return $encoder;
    }

    /**
     * @inheritDoc
     */
    public function createRequest(
        ServerRequestInterface $httpRequest,
        RequestInterpreterInterface $intepreter,
        ApiInterface $api
    ) {
        $requestFactory = new RequestFactory($this);

        return $requestFactory->build($httpRequest, $intepreter, $api);
    }

}
