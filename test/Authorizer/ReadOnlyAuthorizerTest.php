<?php

/**
 * Copyright 2016 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Authorizer;

use CloudCreativity\JsonApi\Object\Resource;
use CloudCreativity\JsonApi\Object\StandardObject;
use CloudCreativity\JsonApi\TestCase;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;

final class ReadOnlyAuthorizerTest extends TestCase
{

    public function testReadOnly()
    {
        /** @var EncodingParametersInterface $parameters */
        $parameters = $this->getMockBuilder(EncodingParametersInterface::class)->getMock();
        $authorizer = new ReadOnlyAuthorizer();
        $record = new StandardObject();

        $this->assertTrue($authorizer->canReadMany($parameters));
        $this->assertTrue($authorizer->canRead($record, $parameters));
        $this->assertTrue($authorizer->canReadRelationship('posts', $record, $parameters));

        $this->assertFalse($authorizer->canCreate(new Resource(), $parameters));
        $this->assertFalse($authorizer->canUpdate($record, $parameters));
        $this->assertFalse($authorizer->canDelete($record, $parameters));
        $this->assertFalse($authorizer->canReplaceRelationship('posts', $record, $parameters));
        $this->assertFalse($authorizer->canAddToRelationship('posts', $record, $parameters));
        $this->assertFalse($authorizer->canRemoveFromRelationship('posts', $record, $parameters));
    }
}
