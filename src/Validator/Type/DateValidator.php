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

namespace CloudCreativity\JsonApi\Validator\Type;

use CloudCreativity\JsonApi\Error\ErrorObject;

/**
 * Class DateValidator
 * @package CloudCreativity\JsonApi
 */
class DateValidator extends TypeValidator
{

    const FORMATS = 'formats';

    const FORMAT_ISO_8601 = \DateTime::ISO8601;
    const FORMAT_ISO_8601_MILLISECONDS = 'Y-m-d\TH:i:s.uO';

    /**
     * @var array
     */
    private $formats = [];

    /**
     * @param array $formats
     * @param bool|false $nullable
     */
    public function __construct(array $formats = [], $nullable = false)
    {
        parent::__construct($nullable);

        if (empty($formats)) {
            $formats = [
                static::FORMAT_ISO_8601,
                static::FORMAT_ISO_8601_MILLISECONDS,
            ];
        }

        $this->setFormats($formats);

        $this->updateTemplate(static::ERROR_INVALID_VALUE, [
            ErrorObject::DETAIL => 'Expecting a valid date value.',
        ]);
    }

    /**
     * @param array $formats
     * @return $this
     */
    public function setFormats(array $formats)
    {
        foreach ($formats as $format) {
            $this->addFormat($format);
        }

        return $this;
    }

    /**
     * @param $format
     * @return $this
     */
    public function addFormat($format)
    {
        if (!is_string($format) || empty($format)) {
            throw new \InvalidArgumentException('Expecting a non-empty string format.');
        }

        $this->formats[] = $format;

        return $this;
    }

    /**
     * @return array
     */
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure(array $config)
    {
        parent::configure($config);

        if (isset($config[static::FORMATS]) && is_array($config[static::FORMATS])) {
            $this->setFormats($config[static::FORMATS]);
        } elseif (isset($config[static::FORMATS]) && is_string($config[static::FORMATS])) {
            $this->addFormat($config[static::FORMATS]);
        }

        return $this;
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isType($value)
    {
        if (!is_string($value) || empty($value)) {
            return false;
        }

        foreach ($this->getFormats() as $format) {

            $date = \DateTime::createFromFormat($format, $value);

            if ($date instanceof \DateTime) {
                return true;
            }
        }

        return false;
    }
}
