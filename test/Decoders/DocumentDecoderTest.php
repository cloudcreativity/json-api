<?php

namespace CloudCreativity\JsonApi\Decoders;

use CloudCreativity\JsonApi\Error\ThrowableError;

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

        $this->setExpectedException(ThrowableError::class);
        $decoder->decode($content);
    }
}
