<?php

namespace CloudCreativity\JsonApi\Document;

use CloudCreativity\JsonApi\TestCase;
use Neomerx\JsonApi\Document\Link;

class ErrorTest extends TestCase
{

    public function testCreate()
    {
        $id = '123';
        $aboutLink = new Link('/api/errors/123');
        $status = '500';
        $code = 'error_code';
        $title = 'An Error';
        $detail = 'This is the error detail';
        $meta = ['foo' => 'bar'];
        $param = 'foobar';

        $error = Error::create([
            Error::ID => $id,
            Error::LINKS => [
                Error::LINKS_ABOUT => $aboutLink,
            ],
            Error::STATUS => $status,
            Error::CODE => $code,
            Error::TITLE => $title,
            Error::DETAIL => $detail,
            Error::SOURCE => [
                Error::SOURCE_PARAMETER => $param,
            ],
            Error::META => $meta,
        ]);

        $this->assertEquals($id, $error->getId(), 'Invalid id');
        $this->assertEquals([Error::LINKS_ABOUT => $aboutLink], $error->getLinks(), 'Invalid links');
        $this->assertEquals($status, $error->getStatus(), 'Invalid status');
        $this->assertEquals($code, $error->getCode(), 'Invalid code');
        $this->assertEquals($title, $error->getTitle(), 'Invalid title');
        $this->assertEquals($detail, $error->getDetail(), 'Invalid detail');
        $this->assertEquals([Error::SOURCE_PARAMETER => $param], $error->getSource(), 'Invalid source');
        $this->assertEquals($meta, $error->getMeta(), 'Invalid meta');
    }

    public function testPartialCreate()
    {
        $title = 'Error Title';
        $status = '422';
        $code = 'illogical';

        $error = Error::create([
            Error::TITLE => $title,
            Error::STATUS => $status,
            Error::CODE => $code,
        ]);

        $this->assertEquals($title, $error->getTitle(), 'Invalid title');
        $this->assertEquals($status, $error->getStatus(), 'Invalid status');
        $this->assertEquals($code, $error->getCode(), 'Invalid code');
    }
}
