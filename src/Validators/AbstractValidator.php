<?php

/**
 * Copyright 2016 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Validators;

use CloudCreativity\JsonApi\Contracts\Validators\ValidationMessageFactoryInterface;
use CloudCreativity\JsonApi\Document\Error;
use CloudCreativity\JsonApi\Helpers\ErrorsAwareTrait;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Contracts\Document\LinkInterface;
use Neomerx\JsonApi\Exceptions\ErrorCollection;

abstract class AbstractValidator
{

    use ErrorsAwareTrait;

    /**
     * @var ValidationMessageFactoryInterface
     */
    protected $messages;

    /**
     * AbstractValidator constructor.
     * @param ValidationMessageFactoryInterface $messages
     */
    public function __construct(ValidationMessageFactoryInterface $messages)
    {
        $this->messages = $messages;
    }

    /**
     * @param $messageKey
     * @param array $messageValues
     */
    protected function addDataError($messageKey, array $messageValues = [])
    {
        $error = $this->messages->error($messageKey, $messageValues);

        $this->errors()->addDataError(
            $error->getTitle(),
            $error->getDetail(),
            $error->getStatus(),
            $error->getId(),
            $this->aboutLink($error),
            $error->getCode(),
            $error->getMeta()
        );
    }

    /**
     * @param $messageKey
     * @param array $messageValues
     * @param $status
     *      the status if defined by the Json-Api spec (i.e. force a specific status code).
     */
    protected function addDataTypeError($messageKey, array $messageValues = [], $status = null)
    {
        $error = $this->messages->error($messageKey, $messageValues);

        $this->errors()->addDataTypeError(
            $error->getTitle(),
            $error->getDetail(),
            $status ?: $error->getStatus(),
            $error->getId(),
            $this->aboutLink($error),
            $error->getCode(),
            $error->getMeta()
        );
    }

    /**
     * @param $messageKey
     * @param array $messageValues
     * @param null $status
     *      the status if defined by the Json-Api spec (i.e. force a specific status code).
     */
    protected function addDataIdError($messageKey, array $messageValues = [], $status = null)
    {
        $error = $this->messages->error($messageKey, $messageValues);

        $this->errors()->addDataIdError(
            $error->getTitle(),
            $error->getDetail(),
            $status ?: $error->getStatus(),
            $error->getId(),
            $this->aboutLink($error),
            $error->getCode(),
            $error->getMeta()
        );
    }

    /**
     * @param $messageKey
     * @param $messageValues
     */
    protected function addDataAttributesError($messageKey, array $messageValues = [])
    {
        $error = $this->messages->error($messageKey, $messageValues);
        $pointer = sprintf('/%s/%s', DocumentInterface::KEYWORD_DATA, DocumentInterface::KEYWORD_ATTRIBUTES);

        $this->errors()->add($this->withPointer($error, $pointer));
    }

    /**
     * @param $attributesKey
     * @param $messageKey
     * @param array $messageValues
     */
    protected function addDataAttributeError($attributesKey, $messageKey, array $messageValues = [])
    {
        $error = $this->messages->error($messageKey, $messageValues);

        $this->errors()->addDataAttributeError(
            $attributesKey,
            $error->getTitle(),
            $error->getDetail(),
            $error->getStatus(),
            $error->getId(),
            $this->aboutLink($error),
            $error->getCode(),
            $error->getMeta()
        );
    }

    /**
     * @param $messageKey
     * @param array $messageValues
     */
    protected function addDataRelationshipsError($messageKey, array $messageValues = [])
    {
        $error = $this->messages->error($messageKey, $messageValues);

        $this->errors()->addRelationshipsError(
            $error->getTitle(),
            $error->getDetail(),
            $error->getStatus(),
            $error->getId(),
            $this->aboutLink($error),
            $error->getCode(),
            $error->getMeta()
        );
    }

    /**
     * @param $relationshipKey
     * @param $messageKey
     * @param array $messageValues
     */
    public function addDataRelationshipError($relationshipKey, $messageKey, array $messageValues = [])
    {
        $error = $this->messages->error($messageKey, $messageValues);

        $this->errors()->addRelationshipError(
            $relationshipKey,
            $error->getTitle(),
            $error->getDetail(),
            $error->getStatus(),
            $error->getId(),
            $this->aboutLink($error),
            $error->getCode(),
            $error->getMeta()
        );
    }

    /**
     * @param $relationshipKey
     * @param ErrorCollection $errors
     */
    protected function addDataRelationshipErrors($relationshipKey, ErrorCollection $errors)
    {
        /** @var ErrorInterface $error */
        foreach ($errors as $error) {
            $this->errors()->addRelationshipError(
                $relationshipKey,
                $error->getTitle(),
                $error->getDetail(),
                $error->getStatus(),
                $error->getId(),
                $this->aboutLink($error),
                $error->getCode(),
                $error->getMeta()
            );
        }
    }

    /**
     * @param ErrorInterface $error
     * @return LinkInterface|null
     */
    private function aboutLink(ErrorInterface $error)
    {
        $links = (array) $error->getLinks();
        $about = isset($links[Error::LINKS_ABOUT]) ? $links[Error::LINKS_ABOUT] : null;

        return ($about instanceof LinkInterface) ? $about : null;
    }

    /**
     * @param ErrorInterface $error
     * @param $pointer
     * @return Error
     */
    private function withPointer(ErrorInterface $error, $pointer)
    {
        return new Error(
            $error->getId(),
            $this->aboutLink($error),
            $error->getStatus(),
            $error->getCode(),
            $error->getTitle(),
            $error->getDetail(),
            [Error::SOURCE_POINTER => $pointer],
            $error->getMeta()
        );
    }
}
