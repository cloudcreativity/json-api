<?php

namespace CloudCreativity\JsonApi\Testing;

use Closure;
use CloudCreativity\JsonApi\TestCase as BaseTestCase;
use PHPUnit_Framework_AssertionFailedError;

class TestCase extends BaseTestCase
{

    /**
     * @param Closure $closure
     * @param string $message
     */
    protected function willFail(Closure $closure, $message = '')
    {
        $didFail = false;

        try {
            $closure();
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $didFail = true;
        }

        $this->assertTrue($didFail, $message ?: 'Expecting test to fail.');
    }
}
