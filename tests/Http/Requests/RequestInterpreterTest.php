<?php

/**
 * Copyright 2017 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Http\Requests;

use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterpreterInterface;
use CloudCreativity\JsonApi\TestCase;

/**
 * Class RequestTest
 *
 * @package CloudCreativity\JsonApi
 */
class RequestInterpreterTest extends TestCase
{

    /**
     * @var RequestInterpreterInterface
     */
    private $request;

    public function testIsIndex()
    {
        $this->willSee('GET')
            ->assertRequestType('index')
            ->assertDocumentNotExpected();
    }

    public function testIsCreateResource()
    {
        $this->willSee('POST')
            ->assertRequestType('createResource')
            ->assertDocumentExpected();
    }

    public function testIsReadResource()
    {
        $this->willSee('GET', '1')
            ->assertRequestType('readResource')
            ->assertDocumentNotExpected();
    }

    public function testIsUpdateResource()
    {
        $this->willSee('PATCH', '1')
            ->assertRequestType('updateResource')
            ->assertDocumentExpected();
    }

    public function testIsDeleteResource()
    {
        $this->willSee('DELETE', '1')
            ->assertRequestType('deleteResource')
            ->assertDocumentNotExpected();
    }

    public function testIsReadRelatedResource()
    {
        $this->willSee('GET', '1', 'comments')
            ->assertRequestType('readRelatedResource')
            ->assertDocumentNotExpected();
    }

    public function testIsReadRelationship()
    {
        $this->willSee('GET', '1', 'comments', true)
            ->assertRequestType('readRelationship')
            ->assertDocumentNotExpected();
    }

    public function testIsReplaceRelationship()
    {
        $this->willSee('PATCH', '1', 'comments', true)
            ->assertRequestType('replaceRelationship')
            ->assertDocumentExpected();
    }

    public function testIsAddToRelationship()
    {
        $this->willSee('POST', '1', 'comments', true)
            ->assertRequestType('addToRelationship')
            ->assertDocumentExpected();
    }

    public function testIsRemoveFromRelationship()
    {
        $this->willSee('DELETE', '1', 'comments', true)
            ->assertRequestType('removeFromRelationship')
            ->assertDocumentExpected();
    }

    /**
     * @param $requestType
     * @return $this
     */
    private function assertRequestType($requestType)
    {
        $checker = 'is' . ucfirst($requestType);

        $methods = [
            'isIndex',
            'isCreateResource',
            'isReadResource',
            'isUpdateResource',
            'isDeleteResource',
            'isReadRelatedResource',
            'isReadRelationship',
            'isReplaceRelationship',
            'isAddToRelationship',
            'isRemoveFromRelationship',
        ];

        foreach ($methods as $method) {
            $message = sprintf('Calling %s for %s', $method, $requestType);
            $expected = ($checker === $method);
            $actual = call_user_func([$this->request, $method]);
            $this->assertSame($expected, $actual, $message);
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function assertDocumentExpected()
    {
        $this->assertTrue($this->request->isExpectingDocument(), 'Document should be expected');

        return $this;
    }

    /**
     * @return $this
     */
    private function assertDocumentNotExpected()
    {
        $this->assertFalse($this->request->isExpectingDocument(), 'Document should not be expected');

        return $this;
    }

    /**
     * @param string $method
     * @param string|null $resourceId
     * @param string|null $relationshipName
     * @param bool $relationshipData
     * @return $this
     */
    private function willSee($method, $resourceId = null, $relationshipName = null, $relationshipData = false)
    {
        $mock = $this->getMockForAbstractClass(AbstractRequestInterpreter::class);

        $mock->method('isMethod')->willReturnCallback(function ($m) use ($method) {
            return strtolower($method) === strtolower($m);
        });

        $mock->method('getResourceId')->willReturn($resourceId);
        $mock->method('getRelationshipName')->willReturn($relationshipName);
        $mock->method('isRelationshipData')->willReturn($relationshipData);

        $this->request = $mock;

        return $this;
    }

}
