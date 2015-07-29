<?php

namespace CloudCreativity\JsonApi\Validator\ResourceIdentifier;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;

class ExpectedTypeValidatorTest extends \PHPUnit_Framework_TestCase
{

    const EXPECTED = 'Foo';
    const NOT_EXPECTED = 'Bar';

    public function testSet()
    {
        $validator = new ExpectedTypeValidator();

        $this->assertSame($validator, $validator->setExpected(static::EXPECTED));
        $this->assertSame(static::EXPECTED, $validator->getExpected());

        return $validator;
    }

    /**
     * @depends testSet
     */
    public function testValid(ExpectedTypeValidator $validator)
    {
        $this->assertTrue($validator->isValid(static::EXPECTED));
        $this->assertTrue($validator->getErrors()->isEmpty());
    }

    /**
     * @depends testSet
     */
    public function testInvalidFormat(ExpectedTypeValidator $validator)
    {
        $this->assertFalse($validator->isValid(123));

        /** @var ErrorInterface $error */
        $error = current($validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorInterface::class, $error);
        $this->assertEquals(ExpectedTypeValidator::INVALID_VALUE, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    /**
     * @depends testSet
     */
    public function testUnexpectedType(ExpectedTypeValidator $validator)
    {
        $this->assertFalse($validator->isValid(static::NOT_EXPECTED));

        /** @var ErrorInterface $error */
        $error = current($validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorInterface::class, $error);
        $this->assertEquals(ExpectedTypeValidator::UNSUPPORTED_TYPE, $error->getCode());
        $this->assertEquals(409, $error->getStatus());
    }
}
