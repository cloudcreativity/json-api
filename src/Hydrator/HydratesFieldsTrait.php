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

namespace CloudCreativity\JsonApi\Hydrator;

use CloudCreativity\JsonApi\Utils\Str;

/**
 * Trait HydratesFieldsTrait
 *
 * @package CloudCreativity\JsonApi
 */
trait HydratesFieldsTrait
{

    /**
     * Get the method name for hydrating a field.
     *
     * @param $fieldName
     * @return string
     */
    protected function methodForField($fieldName)
    {
        return 'hydrate' . Str::classify($fieldName) . 'Field';
    }

    /**
     * Call a method for a resource's field, if it exists.
     *
     * @param $fieldName
     * @param array ...$arguments
     * @return bool
     *      whether a method was invoked.
     */
    protected function callMethodForField($fieldName, ...$arguments)
    {
        return $this->tryInvoke($this->methodForField($fieldName), $arguments);
    }

    /**
     * Get the method name for adding to a field (has-many relationship).
     *
     * @param $fieldName
     * @return string
     */
    protected function methodForAddToField($fieldName)
    {
        return 'addTo' . Str::classify($fieldName) . 'Field';
    }

    /**
     * Call a method to add to a resource's field, if it exists.
     *
     * @param $fieldName
     * @param array ...$arguments
     * @return bool
     */
    protected function callMethodForAddToField($fieldName, ...$arguments)
    {
        return $this->tryInvoke($this->methodForAddToField($fieldName), $arguments);
    }

    /**
     * Get the method name for removing from a field (has-many relationship).
     *
     * @param $fieldName
     * @return string
     */
    protected function methodForRemoveFromField($fieldName)
    {
        return 'removeFrom' . Str::classify($fieldName) . 'Field';
    }

    /**
     * Call a method to remove from a resource's field, if it exists.
     *
     * @param $fieldName
     * @param array ...$arguments
     * @return bool
     */
    protected function callMethodForRemoveFromField($fieldName, ...$arguments)
    {
        return $this->tryInvoke($this->methodForRemoveFromField($fieldName), $arguments);
    }

    /**
     * Try to invoke the supplied method name on this object.
     *
     * @param $method
     * @param array $arguments
     * @return bool
     *      whether the method was invoked.
     */
    private function tryInvoke($method, array $arguments)
    {
        if (!$method || !method_exists($this, $method)) {
            return false;
        }

        call_user_func_array([$this, $method], $arguments);

        return true;
    }
}
