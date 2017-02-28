<?php namespace CloudCreativity\JsonApi\Schema;

use \InvalidArgumentException;
use \Neomerx\JsonApi\I18n\Translator as T;
use Neomerx\JsonApi\Schema\Container as BaseContainer;
use ReflectionClass;

/**
 * @package CloudCreativity\JsonApi
 */
class Container extends BaseContainer
{
    const JSON_API_SCHEMA = 'JSON_API_SCHEMA';

    /**
     * @inheritdoc
     */
    public function getSchemaByType($type)
    {
        is_string($type) === true ?: Exceptions::throwInvalidArgument('type', $type);

        if ($this->hasCreatedProvider($type) === true) {
            return $this->getCreatedProvider($type);
        }

        // If it does not exist try register using reflection
        if ($this->hasProviderMapping($type) === false) {
            $schema = (new ReflectionClass($type))->getConstant(static::JSON_API_SCHEMA);
            if (! $schema) {
                throw new InvalidArgumentException(T::t('Schema is not registered for type \'%s\'.', [$type]));
            }
            $this->setProviderMapping($type, $schema);
        }

        $classNameOrClosure = $this->getProviderMapping($type);
        if ($classNameOrClosure instanceof Closure) {
            $schema = $this->createSchemaFromClosure($classNameOrClosure);
        } else {
            $schema = $this->createSchemaFromClassName($classNameOrClosure);
        }
        $this->setCreatedProvider($type, $schema);

        /** @var SchemaProviderInterface $schema */

        $this->setResourceToJsonTypeMapping($schema->getResourceType(), $type);

        return $schema;
    }
}