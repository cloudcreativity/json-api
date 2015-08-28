<?php

/**
 * Copyright 2015 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CloudCreativity\JsonApi\Contracts\Error;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Contracts\Schema\LinkInterface;

/**
 * Interface ErrorObjectInterface
 * @package CloudCreativity\JsonApi
 */
interface ErrorObjectInterface extends ErrorInterface
{

    const ID = 'id';
    const LINKS = 'links';
    const STATUS = 'status';
    const CODE = 'code';
    const TITLE = 'title';
    const DETAIL = 'detail';
    const SOURCE = 'source';
    const META = 'meta';

    /**
     * @param int|string|null $id
     * @return $this
     */
    public function setId($id);

    /**
     * @param $links
     * @return mixed
     */
    public function setLinks($links);

    /**
     * @param null|string[]|LinkInterface[] $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * @param string|null $code
     * @return $this
     */
    public function setCode($code);

    /**
     * @param string|null $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * @param string|null $detail
     * @return $this
     */
    public function setDetail($detail);

    /**
     * @param SourceObjectInterface|array|null $source
     * @return $this
     */
    public function setSource($source);

    /**
     * Get the source object.
     *
     * This method is provided because `ErrorInterface` already defines `getSource` as providing various different
     * types. By using this method, client code can chain off it knowing it will definitely provide a source object
     * instance.
     *
     * @return SourceObjectInterface
     */
    public function getSourceObject();

    /**
     * @param array|null $meta
     * @return $this
     */
    public function setMeta($meta);
}
