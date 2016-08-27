# Change Log
All notable changes to this project will be documented in this file. This project adheres to
[Semantic Versioning](http://semver.org/) and [this changelog format](http://keepachangelog.com/).

## [Unreleased]

### Added
- Added a `RequestInterpreterInterface` and an `AbstractRequestInterpreter` class. This interface determines what
'type' of JSON API request the current request is. The abstract implementation means there is minimal framework 
integration required.
- A new trait `ErrorCreatorTrait` is now available. This allows an object to construct the errors it contains either
directly from error objects or by using string keys that load errors from the error repository. The recommended 
approach is to always load errors via an error repository.
- New validator - `RelationshipValidator` - that validates that a relationships object is either a has-one or a has-many
relationship. I.e. that it is a valid relationship object according to the JSON API spec.

### Changed
- An acceptable relationship callback or class implementing `AceptRelatedResourceInterface` can now return
an error or error collection instead of a boolean. This allows a custom error message to be returned.
- `AbstractHydrator::methodForRelationship()` is no longer abstract. The default implementation camel cases a 
relationship key, prepends `hydrate` and appends `Relationship`.
- `AbstractAuthorizer` now uses the new `ErrorCreatorTrait` instead of the `ErrorsAwareTrait`, so that authorizers can
load authorization errors from the error repository.
- Improved `ValidatorProviderInterface` so that a provider receives the URL arguments from a request. This means that
a validator provider can now be used for multiple resource types if required.
- Can now construct generic validators to check a document is valid according to the JSON API spec without knowing
any *business logic* - i.e. that the document is semantically correct. The validator factory can now make:
  - a generic resource validator, plus the resource validator argument is now optional
  when creating a resource document validator; and
  - a generic relationship validator, plus the relationship validator argument is now optional when creating a 
  relationship document validator.
- A generic validator provider is now available that will validate any request against the JSON API spec using these
  default validators.

## [0.5.2] - 2016-08-09

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
