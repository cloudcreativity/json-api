<?php

namespace CloudCreativity\JsonApi\Validators;

use CloudCreativity\JsonApi\Object\Document\Document;
use CloudCreativity\JsonApi\TestCase as BaseTestCase;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Decoders\ObjectDecoder;
use Neomerx\JsonApi\Exceptions\ErrorCollection;

class TestCase extends BaseTestCase
{

    /**
     * @var ValidationMessageFactory
     */
    protected $messages;

    /**
     * @var ValidatorFactory
     */
    protected $factory;

    /**
     * @var ObjectDecoder
     */
    protected $decoder;

    /**
     * @return void
     */
    protected function setUp()
    {
        $config = require __DIR__ . '/../../config/validation.php';
        $this->messages = new ValidationMessageFactory($config);
        $this->factory = new ValidatorFactory($this->messages);
        $this->decoder = new ObjectDecoder();
    }

    /**
     * @param $content
     * @return Document
     */
    protected function decode($content)
    {
        $obj = $this->decoder->decode($content);

        return new Document($obj);
    }

    /**
     * @param ErrorCollection $errors
     * @param $pointer
     * @param $messageKey
     */
    protected function assertErrorAt(ErrorCollection $errors, $pointer, $messageKey)
    {
        $error = $this->findErrorAt($errors, $pointer);
        $expected = $this->messages->error($messageKey);

        $this->assertEquals($expected->getId(), $error->getId(), 'Unexpected error id.');
        $this->assertEquals($expected->getTitle(), $error->getTitle(), 'Unexpected error title.');
        $this->assertEquals($expected->getCode(), $error->getCode(), 'Unexpected error code.');
        $this->assertEquals($expected->getStatus(), $error->getStatus(), 'Unexpected error status.');
    }

    /**
     * @param ErrorCollection $errors
     * @param $pointer
     * @param $needle
     */
    protected function assertDetailContains(ErrorCollection $errors, $pointer, $needle)
    {
        $error = $this->findErrorAt($errors, $pointer);

        $this->assertContains($needle, $error->getDetail(), "Invalid detail for error: $pointer");
    }

    /**
     * @param ErrorCollection $errors
     * @param $pointer
     * @return ErrorInterface|null
     */
    protected function findErrorAt(ErrorCollection $errors, $pointer)
    {
        /** @var ErrorInterface $error */
        foreach ($errors as $error) {
            $source = (array) $error->getSource();
            $check = isset($source[ErrorInterface::SOURCE_POINTER])
                ? $source[ErrorInterface::SOURCE_POINTER] : null;

            if ($pointer === $check) {
                return $error;
            }
        }

        $pointers = implode(', ', $this->pointers($errors));
        $this->fail("$pointer not in pointers: [$pointers]");

        return null;
    }

    /**
     * @param ErrorCollection $errors
     * @return array
     */
    protected function pointers(ErrorCollection $errors)
    {
        $pointers = [];

        /** @var ErrorInterface $error */
        foreach ($errors as $error) {
            $source = (array) $error->getSource();
            $pointer = isset($source[ErrorInterface::SOURCE_POINTER])
                ? $source[ErrorInterface::SOURCE_POINTER] : null;

            if ($pointer) {
                $pointers[] = $pointer;
            }
        }

        return array_unique($pointers);
    }
}
