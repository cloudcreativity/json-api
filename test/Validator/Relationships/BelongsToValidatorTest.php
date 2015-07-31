<?php

namespace CloudCreativity\JsonApi\Validator\Relationships;

use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Object\Relationships\Relationship;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;

class BelongsToValidatorTest extends \PHPUnit_Framework_TestCase
{

  const TYPE = 'foo';
  const INVALID_TYPE = 'bar';

  const ID = 123;
  const INVALID_ID = 456;

  protected $valid;
  protected $validator;

  protected function setUp()
  {
    $this->valid = new \stdClass();
    $this->valid->{Relationship::DATA} = new \stdClass();
    $this->valid->{Relationship::DATA}->{ResourceIdentifier::TYPE} = static::TYPE;
    $this->valid->{Relationship::DATA}->{ResourceIdentifier::ID} = static::ID;

    $this->validator = new BelongsToValidator();
    $this->validator->setTypes(static::TYPE);
  }

  public function testValid()
  {
    $this->assertSame($this->validator, $this->validator->setTypes(static::TYPE));
    $this->assertTrue($this->validator->isValid($this->valid));
  }

  public function testMultiTypeValid()
  {
    $stack = ['foo-bar', 'baz-bat'];
    $validator = new BelongsToValidator();

    $validator->setTypes($stack);

    foreach ($stack as $type) {
      $this->valid->{Relationship::DATA}->{ResourceIdentifier::TYPE} = $type;
      $this->assertTrue($validator->isValid($this->valid), sprintf('Expecting type %s to be valid.', $type));
    }
  }

  public function testInvalidType()
  {
    $this->valid->{Relationship::DATA}->{ResourceIdentifier::TYPE} = static::INVALID_TYPE;
    $this->validator->setTypes(static::TYPE);

    $this->assertFalse($this->validator->isValid($this->valid));

    /** @var ErrorObject $error */
    $error = current($this->validator->getErrors()->getAll());

    $this->assertInstanceOf(ErrorObject::class, $error);
    $this->assertEquals(BelongsToValidator::ERROR_INVALID_TYPE, $error->getCode());
    $this->assertEquals(400, $error->getStatus());
    $this->assertEquals('/data/type', $error->source()->getPointer());
  }

  public function testInvalidId()
  {
    $this->valid->{Relationship::DATA}->{ResourceIdentifier::ID} = null;
    $this->assertFalse($this->validator->isValid($this->valid));

    /** @var ErrorObject $error */
    $error = current($this->validator->getErrors()->getAll());

    $this->assertInstanceOf(ErrorObject::class, $error);
    $this->assertEquals(BelongsToValidator::ERROR_INVALID_ID, $error->getCode());
    $this->assertEquals(400, $error->getStatus());
    $this->assertEquals('/data/id', $error->source()->getPointer());
  }

  public function testHasMany()
  {
    $this->valid->{Relationship::DATA} = [];
    $this->assertFalse($this->validator->isValid($this->valid));

    /** @var ErrorObject $error */
    $error = current($this->validator->getErrors()->getAll());

    $this->assertInstanceOf(ErrorObject::class, $error);
    $this->assertEquals(BelongsToValidator::ERROR_INVALID_VALUE, $error->getCode());
    $this->assertEquals(400, $error->getStatus());
    $this->assertEquals('/data', $error->source()->getPointer());
  }

  public function testNullIsValid()
  {
    $this->valid->{Relationship::DATA} = null;
    $this->assertTrue($this->validator->isValid($this->valid));
  }

  public function testDoNotAcceptNull()
  {
    $this->valid->{Relationship::DATA} = null;
    $this->assertSame($this->validator, $this->validator->setAllowEmpty(false));
    $this->assertFalse($this->validator->isValid($this->valid));

    /** @var ErrorObject $error */
    $error = current($this->validator->getErrors()->getAll());

    $this->assertInstanceOf(ErrorObject::class, $error);
    $this->assertEquals(BelongsToValidator::ERROR_NULL_DISALLOWED, $error->getCode());
    $this->assertEquals(422, $error->getStatus());
    $this->assertEquals('/data', $error->source()->getPointer());
  }

  public function testCallback()
  {
    $invoked = false;
    $callback = function (ResourceIdentifier $identifier) use (&$invoked) {
      $this->assertSame(static::TYPE, $identifier->getType());
      $this->assertSame(static::ID, $identifier->getId());
      $invoked = true;
      return true;
    };

    $this->assertSame($this->validator, $this->validator->setCallback($callback));
    $this->assertTrue($this->validator->isValid($this->valid));
    $this->assertTrue($invoked, 'Expecting callback to have been invoked.');
  }

  /**
   * @depends testCallback
   */
  public function testCallbackInvalid()
  {
    $this->validator->setCallback(function () {
      return false;
    });

    $this->assertFalse($this->validator->isValid($this->valid));

    /** @var ErrorObject $error */
    $error = current($this->validator->getErrors()->getAll());

    $this->assertInstanceOf(ErrorObject::class, $error);
    $this->assertEquals(BelongsToValidator::ERROR_NOT_FOUND, $error->getCode());
    $this->assertEquals(404, $error->getStatus());
    $this->assertEquals('/data', $error->source()->getPointer());
  }
}
