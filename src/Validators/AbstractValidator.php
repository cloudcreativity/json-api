<?php

namespace CloudCreativity\JsonApi\Validators;

use CloudCreativity\JsonApi\Contracts\Validators\ValidationMessageFactoryInterface;
use CloudCreativity\JsonApi\Document\Error;
use CloudCreativity\JsonApi\Helpers\ErrorsAwareTrait;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Contracts\Document\LinkInterface;

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
        $pointer = sprintf('/%s/%s', DocumentInterface::KEYWORD_DATA, DocumentInterface::KEYWORD_RELATIONSHIPS);

        $this->errors()->add($this->withPointer($error, $pointer));
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
