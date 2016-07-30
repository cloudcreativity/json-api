# Change Log
All notable changes to this project will be documented in this file. This project adheres to
[Semantic Versioning](http://semver.org/) and [this changelog format](http://keepachangelog.com/).

## [Unreleased]

### Fixed

- Bug in resources tester that prevented normalization of ids if an integer was passed for ids.

## [0.5.1] - 2016-07-27

### Added

- Testers for a resource and multiple resources (resource collections). These tester can be obtained via
methods on the document tester.
- Hydration hooks in `AbstractHydrator` so that it is easier to implement pre- and post-hydration logic.

## [0.5.0] - 2016-07-20

This is a substantial refactoring, based on using this package in production environments. We also updated 
the underlying package `neomerx/json-api` to from `v0.6.6` to `^0.8.0`, which is a breaking change.
See the [Upgrade Notes](UPGRADE.md) for help.

This is a brief summary of the main changes:

### Added
- New validator interfaces so that a validator is specific as to what part of the JSON API document it is responsible
for validating.
- Validator instances are now built via a validator factory (which has a defined interface). This makes it clearer how
to construct validators that are provided by this package.
- Each resource type in an application will now have a 'validator provider' (`ValidatorProviderInterface`) that 
constructs the validators that are specific for that resource type.
- The concept of a 'store' was added. This enables validators etc to look up domain objects that are referred to in
JSON API resource identifiers (type/id combinations).
- Errors defined in configuration arrays are now loaded via an 'error repository'. Provision of errors from this
service allows your application to implement global error modifiers, e.g. translation, as required.
- An `ApiInterface` now exists to hold the JSON API configuration per API in your application.
- A `HydratorInterface` was added so that the logic for transferring data from a JSON API document into a domain object
can be unitized if desired. This helps reduce the complexity of controllers.
- The `Testing` namespace was added, with some initial test helpers. The test helpers are designed for use with PHPUnit
and enable assertions on the content of JSON API documents returned by your applications. We'll be adding more test
helpers in future releases.

### Removed
- Abstracted framework integration removed in favour of framework packages writing their own integrations that
suit the needs of the particular framework. 
- Error handling changes in the `neomerx/json-api` package resulted in exception renderers being removed.
- All validators in the `Validator` (singular) namespace were removed. Our refactored validator implementation
is in the `Validators` namespace instead.

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
