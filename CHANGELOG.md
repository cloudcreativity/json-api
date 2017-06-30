# Change Log
All notable changes to this project will be documented in this file. This project adheres to
[Semantic Versioning](http://semver.org/) and [this changelog format](http://keepachangelog.com/).

## Unreleased

### Fixed
- A decoder is now only matched if the client has sent a HTTP request with message body.

### Removed
- Deleted the `Utils\NullReporter` class as it was not in use.

## [0.9.0] - 2017-06-07

### Added
- Validation error messages for invalid attributes and query parameters can now be created via the validator error
factory.

### Changed
- Renamed `Resource` to `ResourceObject`, along with associated interfaces, collections and testers. This is because
`resource` is a reserved name in PHP.
- `id` member on a resource object or resource object identifier will now always be returned as a string. Integers
were previously allowed, however the spec states that the `id` member must be a string.

### Removed
- Separated out the standard object implementation into a `cloudcreativity/utils-object` package and removed the
classes, interfaces and utilities from this package.
- Deprecated `RelatedHydratorTrait::callHydrateRelated()` removed.
- The `HttpServiceInterface` was removed as it is no longer required as classes can be directly injected rather
than obtained via the service.

### Fixed
- [#35] Validator was not rejecting a resource identifier with a `null` id. It will now reject an identifier that
does not have a string `id`. The same change has also been implemented in the resource object validator.
- [#30] Attributes that are objects are now cast as standard objects when iterating over attributes.

## [0.8.0] - 2017-05-20

### Added
- A query parameter validator interface that can be injected into a new validation query checker class. This allows
integration of framework specific validators into the query checking process.
- Traits to apply to HTTP middleware are now available in the `Http\Middleware` namespace.

### Changed
- Resource type is now passed to authorizer methods that do not receive a record instance. 
- The `ApiInterface` no longer returns a factory, paging strategy or options. It now returns an errors repository
as errors are specifically scoped to an API.
- Store adapters are now tied to a specific resource type rather than handling multiple. This allows them to deal
with querying (index routes) many resources at once - i.e. filter, pagination logic.
- Pagination has been removed as it is too framework-specific. This package now only contains a single interface -
`PageInterface` - that indicates that data is paginated. The response factory handles this to merge the paginated
data into the encoded response.
- Refactored factories to bring them more in line with the `neomerx/json-api` factory approach.

### Removed
- The `RequestHandlerInterface` and related classes. This helps reduce the number of units per resource-type, as
the functions of this class can be implemented as HTTP middleware.
- The filter validator has been removed in preference of query parameter validation.

## [0.7.2] - 2017-02-27

### Fixed
- Corrected doc blocks in `RequestHandler`

## [0.7.1] - 2017-02-22

### Added
- Extended encoder that allows serialization of JSON API documents to arrays.

## [0.7.0] - 2017-02-20

### Added
- Can now check whether a resource type is known to the store (i.e. is a valid resource type).
- Relationships validators will now reject a resource identifier if the resource type is not known to the store - i.e.
is not valid. A specific validation error message is returned when this is encountered.

### Changed
- The `neomerx/json-api` dependency has been updated to v1.0.

### Removed
- This package no longer supports PHP 5.5.

## [0.6.1] - 2017-02-03

### Added
- New trait for standard hydrating of attributes: `Hydrator\HydratesAttributesTrait`.
- New trait for standard extraction of attributes: `Schema\ExtractsAttributesTrait`.
- Helpers have been added for converting string keys, to aid conversion from the JSON API recommended *dasherized*
format to underscores or camel case. Helpers are on the `Utils\Str` class.
- `RelatedHydratorTrait` now also enables attributes to be mapped during related hydration.

## [0.6.0] - 2016-10-21

### Added
- Added a full suite of request processing classes, as changes to the `ApiInterface` and the addition of a
 `RequestInterface` allow this to be framework-agnostic.
  - A request factory now builds a JSON API request object, throwing JSON API exceptions if anything about the 
   request does not conform to the JSON API specification.
  -`RequestInterpreterInterface` and an `AbstractRequestInterpreter` class. This interface determines what
  'type' of JSON API request the current request is. The abstract implementation means there is minimal framework 
  integration required.
  - `RequestHandlerInterface` validates the request against domain (application) logic. A default request handler is
  provided and applications can compose the logic by injecting it with an authorizer and validator provider.
- Added an `AbstractResponses` class that means very minimal framework integration is required.
- Added a `ResponseFactory` class to provided easy creation of common JSON API responses.
- A new trait `ErrorCreatorTrait` is now available. This allows an object to construct the errors it contains either
directly from error objects or by using string keys that load errors from the error repository. The recommended 
approach is to always load errors via an error repository.
- New validator - `RelationshipValidator` - that validates that a relationships object is either a has-one or a has-many
relationship. I.e. that it is a valid relationship object according to the JSON API spec.
- The store is now backed by an identity map, meaning multiple checks for resource identifiers will not result in
adapters being queried multiple times.
- Allow a document's data member to be returned as a resource collection if it is an array. Added a resource collection
interface and class for this purpose.
- Allow a hydrator to hint that it needs to hydrate related domain records by implementing the `HydratesRelatedInterface`.
This is useful for two-step hydration, which is commonly needed for relational databases.

### Changed
- An acceptable relationship callback or class implementing `AceptRelatedResourceInterface` can now return
an error or error collection instead of a boolean. This allows a custom error message to be returned.
- `AbstractHydrator::methodForRelationship()` is no longer abstract. The default implementation camel cases a 
relationship key, prepends `hydrate` and appends `Relationship`.
- Authorizers now receive the client sent resource in `canUpdate()` and the client sent relationship in 
`canModifyRelationship`. This allows authorization logic to factor in what a client is attempting to change on a 
specific resource if needed.
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
