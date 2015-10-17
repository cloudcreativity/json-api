<?php

namespace CloudCreativity\JsonApi\Decoders;

use CloudCreativity\JsonApi\Error\ErrorException;

class DocumentDecoderTest extends \PHPUnit_Framework_TestCase
{

    public function testInvalidJson()
    {
        $content = <<<EOL
        {
            "data": {
                "type": "foo"
        }
EOL;

        $decoder = new DocumentDecoder();

        $this->setExpectedException(ErrorException::class);
        $decoder->decode($content);
    }
}
