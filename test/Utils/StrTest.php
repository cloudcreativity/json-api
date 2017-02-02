<?php

namespace CloudCreativity\JsonApi\Utils;

use CloudCreativity\JsonApi\TestCase;

class StrTest extends TestCase
{

    /**
     * @return array
     */
    public function dasherizeProvider()
    {
        return [
            ['foo', 'foo'],
            ['foo_bar', 'foo-bar'],
            ['fooBar', 'foo-bar'],
            ['foo-bar', 'foo-bar'],
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider dasherizeProvider
     */
    public function testDasherize($value, $expected)
    {
        $this->assertSame($expected, Str::dasherize($value));
    }

    /**
     * @return array
     */
    public function decamelizeProvider()
    {
        return [
            ['foo', 'foo'],
            ['fooBar', 'foo_bar'],
            ['fooBarBazBat', 'foo_bar_baz_bat'],
            ['foo_bar', 'foo_bar'],
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider decamelizeProvider
     */
    public function testDecamelize($value, $expected)
    {
        $this->assertSame($expected, Str::decamelize($value));
    }

    /**
     * @return array
     */
    public function camelizeProvider()
    {
        return [
            ['foo', 'foo'],
            ['foo-bar', 'fooBar'],
            ['foo_bar', 'fooBar'],
            ['foo_bar_baz_bat', 'fooBarBazBat'],
            ['fooBar', 'fooBar'],
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider camelizeProvider
     */
    public function testCamelizeAndClassify($value, $expected)
    {
        $this->assertSame($expected, Str::camelize($value), 'camelize');
        $this->assertSame(ucfirst($expected), Str::classify($value), 'classify');
    }
}
