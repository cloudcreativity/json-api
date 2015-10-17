<?php

namespace CloudCreativity\JsonApi\Error;

use Exception;

class ErrorExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testWithErrorObject()
    {
        $error = new ErrorObject([
            ErrorObject::TITLE => 'Foo',
            ErrorObject::CODE => 'bar',
        ]);

        $exception = new ErrorException($error);

        $this->assertSame($error, $exception->getError());

        $expected = new ErrorCollection([$error]);

        $this->assertEquals($expected, $exception->getErrors());
    }

    public function testWithArray()
    {
        $arr = [
            ErrorException::TITLE => 'Foo',
            ErrorException::DETAIL => 'Bar',
            ErrorException::CODE => 'baz',
            ErrorException::STATUS => 501,
        ];

        $exception = new ErrorException($arr);
        $expected = new ErrorObject($arr);

        $this->assertEquals($expected, $exception->getError());
    }

    public function testPreviousException()
    {
        $previous = new Exception('My previous exception');
        $exception = new ErrorException(new ErrorObject(), $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}
