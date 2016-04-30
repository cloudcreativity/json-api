# Change Log
All notable changes to this project will be documented in this file. This project adheres to
[Semantic Versioning](http://semver.org/) and [this changelog format](http://keepachangelog.com/).

## [Unreleased]

Updated package `neomerx/json-api` to from `v0.6.6` to `^0.8.0`, which is a breaking change.
See the [Upgrade Notes](UPGRADE.md) for help.

### Removed
- Abstracted framework integration removed in favour of framework packages writing their own integrations that
suit the needs of the particular framework. Classes from the following namespaces were removed:
  - `CloudCreativity\JsonApi\Contracts\Integration`
  - `CloudCreativity\JsonApi\Integration`
- `CloudCreativity\JsonApi\Error\ThrowableError` was deprecated in `v0.4.0` and has now been removed.
- Error handling changes in the `neomerx/json-api` package means the following classes have been removed:
  - `CloudCreativity\JsonApi\Exceptions\ErrorRenderer`
  - `CloudCreativity\JsonApi\Exception\ExceptionThrower`
  - `CloudCreativity\JsonApi\Exceptions\ExceptionThrower`
  - `CloudCreativity\JsonApi\Exceptions\StandardRenderer`

## [0.4.1] - 2016-04-27

### Added
- Standard object `transformKeys` method, to apply a callable transform to keys on the object.
- Standard object `convertValue` and `convertValues` methods. To apply a callable converter to key or keys
on the object.

## [0.4.0] - 2016-01-28

### Added
- Standard object `mapKey` and `mapKeys` helpers, to change keys within the object.

### Deprecated
- `ThrowableError` class is now deprecated. In its place, you should throw an `ErrorException` and can use an
array as the first constructor argument to construct the JSON API error object that is being thrown.
`ThrowableError` will be removed in `v0.5.0`.
