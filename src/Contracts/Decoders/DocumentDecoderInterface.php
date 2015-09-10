<?php

namespace CloudCreativity\JsonApi\Contracts\Decoders;

use CloudCreativity\JsonApi\Contracts\Object\Document\DocumentInterface;
use CloudCreativity\JsonApi\Contracts\Validator\ValidatorAwareInterface;
use Neomerx\JsonApi\Contracts\Decoder\DecoderInterface;

/**
 * Interface DocumentDecoderInterface
 * @package CloudCreativity\JsonApi\Contracts\Decoders
 */
interface DocumentDecoderInterface extends DecoderInterface, ValidatorAwareInterface
{

    /**
     * @param string $content
     * @return DocumentInterface
     */
    public function decodeDocument($content);
}
