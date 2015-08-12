<?php

namespace CloudCreativity\JsonApi\Validator\Resource;

use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Error\ErrorCollection;
use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Object\Resource\Resource;
use Neomerx\JsonApi\Document\Error;

class AbstractResourceValidatorTest extends \PHPUnit_Framework_TestCase
{

    const EXPECTED_TYPE = 'foo';
    const UNEXPECTED_TYPE = 'bar';

    protected $type;

    /**
     * @var AbstractResourceValidator
     */
    protected $validator;

    /**
     * @var \stdClass
     */
    protected $input;

    /**
     * @var ErrorObject
     */
    protected $error;

    protected function setUp()
    {
        $this->validator = $this->getMockForAbstractClass(AbstractResourceValidator::class);
        $this->type = $this->getMock(ValidatorInterface::class);

        $this->validator
            ->method('getTypeValidator')
            ->willReturn($this->type);

        $this->type
            ->method('isValid')
            ->will($this->returnValueMap([
                [static::EXPECTED_TYPE, true],
            ]));

        $this->input = new \stdClass();
        $this->input->{Resource::TYPE} = static::EXPECTED_TYPE;

        $this->error = new ErrorObject();
        $this->error->setTitle('Foo');
        $this->error->setCode('Bar');
    }

    public function testIsValid()
    {
        $this->assertTrue($this->validator->isValid($this->input));
    }

    public function testInvalid()
    {
        $this->assertFalse($this->validator->isValid([]));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(AbstractResourceValidator::ERROR_INVALID_VALUE, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    public function testMissingType()
    {
        $this->assertFalse($this->validator->isValid(new \stdClass()));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());

        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(AbstractResourceValidator::ERROR_MISSING_TYPE, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    public function testTypeValidator()
    {
        $expected = clone $this->error;
        $expected->source()->setPointer('/' . Resource::TYPE);

        $this->type->method('getErrors')->willReturn(new ErrorCollection([$this->error]));

        $this->input->{Resource::TYPE} = static::UNEXPECTED_TYPE;

        $this->assertFalse($this->validator->isValid($this->input));

        $error = current($this->validator->getErrors()->getAll());
        $this->assertEquals($expected, $error);
    }

    public function testMissingId()
    {
        $this->validator
            ->method('getIdValidator')
            ->willReturn($this->getMock(ValidatorInterface::class));

        $this->assertFalse($this->validator->isValid($this->input));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());
        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(AbstractResourceValidator::ERROR_MISSING_ID, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    public function testUnexpectedId()
    {
        $this->input->{Resource::ID} = 123;

        $this->assertFalse($this->validator->isValid($this->input));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());
        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(AbstractResourceValidator::ERROR_UNEXPECTED_ID, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
        $this->assertEquals('/' . Resource::ID, $error->source()->getPointer());
    }

    public function testValidId()
    {
        $id = 123;
        $this->input->{Resource::ID} = $id;

        $idValidator = $this->getMock(ValidatorInterface::class);
        $idValidator->expects($this->once())
            ->method('isValid')
            ->with($id)
            ->willReturn(true);

        $this->validator
            ->method('getIdValidator')
            ->willReturn($idValidator);

        $this->assertTrue($this->validator->isValid($this->input));
    }

    public function testInvalidId()
    {
        $this->input->{Resource::ID} = 123;

        $idValidator = $this->getMock(ValidatorInterface::class);
        $idValidator->method('getErrors')
            ->willReturn(new ErrorCollection([$this->error]));

        $this->validator
            ->method('getIdValidator')
            ->willReturn($idValidator);

        $expected = clone $this->error;
        $expected->source()->setPointer('/' . Resource::ID);

        $this->assertFalse($this->validator->isValid($this->input));

        $error = current($this->validator->getErrors()->getAll());
        $this->assertEquals($expected, $error);
    }

    public function testMissingAttributes()
    {
        $this->validator
            ->method('isExpectingAttributes')
            ->willReturn(true);

        $this->assertFalse($this->validator->isValid($this->input));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());
        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(AbstractResourceValidator::ERROR_MISSING_ATTRIBUTES, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    public function testUnexpectedAttributes()
    {
        $this->input->{Resource::ATTRIBUTES} = new \stdClass();

        $this->assertFalse($this->validator->isValid($this->input));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());
        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(AbstractResourceValidator::ERROR_UNEXPECTED_ATTRIBUTES, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
        $this->assertEquals('/' . Resource::ATTRIBUTES, $error->source()->getPointer());
    }

    public function testValidAttributes()
    {
        $attributes = new \stdClass;
        $attributes->foo = 'bar';

        $this->input->{Resource::ATTRIBUTES} = $attributes;

        $attrValidator = $this->getMock(ValidatorInterface::class);
        $attrValidator->method('isValid')
            ->with($attributes)
            ->willReturn(true);

        $this->validator
            ->method('getAttributesValidator')
            ->willReturn($attrValidator);

        $this->assertTrue($this->validator->isValid($this->input));
    }

    public function testInvalidAttributes()
    {
        $this->input->{Resource::ATTRIBUTES} = new \stdClass();

        $attrValidator = $this->getMock(ValidatorInterface::class);

        $attrValidator->method('isValid')
            ->willReturn(false);

        $attrValidator->method('getErrors')
            ->willReturn(new ErrorCollection([$this->error]));

        $this->validator
            ->method('getAttributesValidator')
            ->willReturn($attrValidator);

        $current = '/foo';
        $this->error->source()->setPointer($current);

        $expected = clone $this->error;
        $expected->source()->setPointer(sprintf('/%s%s', Resource::ATTRIBUTES, $current));

        $this->assertFalse($this->validator->isValid($this->input));

        $error = current($this->validator->getErrors()->getAll());
        $this->assertEquals($expected, $error);
    }

    public function testMissingRelationships()
    {
        $this->validator
            ->method('isExpectingRelationships')
            ->willReturn(true);

        $this->assertFalse($this->validator->isValid($this->input));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());
        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(AbstractResourceValidator::ERROR_MISSING_RELATIONSHIPS, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
    }

    public function testUnexpectedRelationships()
    {
        $this->input->{Resource::RELATIONSHIPS} = new \stdClass();

        $this->assertFalse($this->validator->isValid($this->input));

        /** @var ErrorObject $error */
        $error = current($this->validator->getErrors()->getAll());
        $this->assertInstanceOf(ErrorObject::class, $error);
        $this->assertEquals(AbstractResourceValidator::ERROR_UNEXPECTED_RELATIONSHIPS, $error->getCode());
        $this->assertEquals(400, $error->getStatus());
        $this->assertEquals('/' . Resource::RELATIONSHIPS, $error->source()->getPointer());
    }

    public function testValidRelationships()
    {
        $rel = new \stdClass();
        $rel->foo = 'bar';

        $this->input->{Resource::RELATIONSHIPS} = $rel;

        $relValidator = $this->getMock(ValidatorInterface::class);
        $relValidator->method('isValid')
            ->with($rel)
            ->willReturn(true);

        $this->validator
            ->method('getRelationshipsValidator')
            ->willReturn($relValidator);

        $this->assertTrue($this->validator->isValid($this->input));
    }

    public function testInvalidRelationships()
    {
        $this->input->{Resource::RELATIONSHIPS} = new \stdClass();
        $current = '/foo';
        $this->error->source()->setPointer($current);

        $relValidator = $this->getMock(ValidatorInterface::class);
        $relValidator->method('getErrors')
            ->willReturn(new ErrorCollection([$this->error]));

        $this->validator
            ->method('getRelationshipsValidator')
            ->willReturn($relValidator);

        $expected = clone $this->error;
        $expected->source()->setPointer(sprintf('/%s%s', Resource::RELATIONSHIPS, $current));

        $this->assertFalse($this->validator->isValid($this->input));

        $error = current($this->validator->getErrors()->getAll());
        $this->assertEquals($expected, $error);
    }
}