<?php

namespace CloudCreativity\JsonApi\Validator\Relationships;

use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Error\ErrorCollection;
use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;

class RelationshipsValidatorTest extends \PHPUnit_Framework_TestCase
{

  const KEY_A = 'foo';
  const KEY_B = 'bars';

  protected $input;
  protected $a;
  protected $b;

  protected function setUp()
  {
    $this->input = new \stdClass();
    $this->input->{static::KEY_A} = new \stdClass();
    $this->input->{static::KEY_A}->foo = 'bar';
    $this->input->{static::KEY_B} = new \stdClass();
    $this->input->{static::KEY_B}->baz = 'bat';

    $this->a = $this->getMock(ValidatorInterface::class);
    $this->b = $this->getMock(ValidatorInterface::class);

    $this->validator = new RelationshipsValidator();
  }

  public function testValid()
  {
    $this->assertSame($this->validator, $this->validator->setValidators([
      static::KEY_A => $this->a,
      static::KEY_B => $this->b,
    ]));

    $this->a->expects($this->once())
      ->method('isValid')
      ->with($this->input->{static::KEY_A})
      ->willReturn(true);

    $this->b->expects($this->once())
      ->method('isValid')
      ->with($this->input->{static::KEY_B})
      ->willReturn(true);

    $this->assertTrue($this->validator->isValid($this->input));
  }

  public function testInvalidValue()
  {
    $this->assertFalse($this->validator->isValid(null));

    /** @var ErrorObject $error */
    $error = current($this->validator->getErrors()->getAll());

    $this->assertInstanceOf(ErrorObject::class, $error);
    $this->assertEquals(RelationshipsValidator::ERROR_INVALID_VALUE, $error->getCode());
    $this->assertEquals(400, $error->getStatus());
  }

  public function testInvalidKey()
  {
    $pointer = '/error/pointer';

    $err = new ErrorObject();
    $err->setCode('foo')
      ->source()
      ->setPointer($pointer);

    $expected = clone $err;
    $expected->source()->setPointer(sprintf('/%s%s', static::KEY_B, $pointer));

    $this->a->method('isValid')->willReturn(true);
    $this->b->method('isValid')->willReturn(false);
    $this->b->method('getErrors')->willReturn(new ErrorCollection([$err]));

    $this->validator->setValidators([
      static::KEY_A => $this->a,
      static::KEY_B => $this->b,
    ]);

    $this->assertFalse($this->validator->isValid($this->input));

    /** @var ErrorObject $error */
    $error = current($this->validator->getErrors()->getAll());

    $this->assertInstanceOf(ErrorObject::class, $error);
    $this->assertEquals($expected, $error);
  }
}
