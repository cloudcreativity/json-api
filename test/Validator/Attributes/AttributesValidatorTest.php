<?php

namespace CloudCreativity\JsonApi\Validator\Attributes;

use CloudCreativity\JsonApi\Error\ErrorCollection;
use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Error\SourceObject;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;

class AttributesValidatorTest extends \PHPUnit_Framework_TestCase
{

    const KEY_A = 'foo';
    const KEY_B = 'bar';
    const KEY_C = 'baz';

    protected $data;

    protected function setUp()
    {
        $data = new \stdClass();
        $data->{static::KEY_A} = 'foobar';
        $data->{static::KEY_B} = 'bazbat';

        $this->data = $data;
    }

    public function testIsValid()
    {
        $validator = new AttributesValidator();

        $this->assertTrue($validator->isValid($this->data));
        $this->assertTrue($validator->getErrors()->isEmpty());
    }

    public function testNotAllowed()
    {
        $validator = new AttributesValidator();

        $this->assertSame($validator, $validator->setAllowed([static::KEY_A]));
        $this->assertFalse($validator->isValid($this->data));

        /** @var ErrorInterface $error */
        $error = current($validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorInterface::class, $error);
        $this->assertEquals(AttributesValidator::ERROR_UNRECOGNISED_ATTRIBUTE, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
        $this->assertEquals('/' . static::KEY_B, $error->getSource()->getPointer());
    }

    public function testRequiredKeys()
    {
        $validator = new AttributesValidator();
        $this->assertSame($validator, $validator->setRequired([static::KEY_A, static::KEY_B]));
        $this->assertTrue($validator->isValid($this->data));
    }

    public function testMissingRequired()
    {
        $validator = new AttributesValidator();
        $validator->setRequired([static::KEY_A, static::KEY_C]);

        $this->assertFalse($validator->isValid($this->data));

        /** @var ErrorInterface $error */
        $error = current($validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorInterface::class, $error);
        $this->assertEquals(AttributesValidator::ERROR_REQUIRED_ATTRIBUTE, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    public function testKeyValidator()
    {
        $mock = $this->getMock(ValidatorInterface::class);

        $mock->expects($this->once())
            ->method('isValid')
            ->with($this->data->{static::KEY_A})
            ->willReturn(true);

        $validator = new AttributesValidator();

        $this->assertSame($validator, $validator->setValidator(static::KEY_A, $mock));

        $validator->isValid($this->data);
    }

    public function testKeyValidatorInvalid()
    {
        $error = new ErrorObject([
            ErrorObject::CODE => 'test-invalid',
            ErrorObject::STATUS => 400,
            ErrorObject::SOURCE => [
                SourceObject::POINTER => '/extra',
            ]
        ]);

        $expected = clone $error;
        $expected->setSource([
            SourceObject::POINTER => '/' . static::KEY_A . '/extra',
        ]);

        $mock = $this->getMock(ValidatorInterface::class);

        $mock->method('getErrors')
            ->willReturn(new ErrorCollection([$error]));

        $validator = new AttributesValidator();
        $validator->setValidator(static::KEY_A, $mock);

        $this->assertFalse($validator->isValid($this->data));

        /** @var ErrorInterface $err */
        $err = current($validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorInterface::class, $err);
        $this->assertEquals($expected, $err);
    }
}
