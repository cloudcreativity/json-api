# Upgrade Guide

This file provides notes on how to upgrade between breaking versions.

## v0.4 to v0.5

The underlying `neomerx/json-api` package was upgraded from `v0.6.6` to `v0.8.0`.
[Please refer to these notes.](https://github.com/neomerx/json-api/wiki/Upgrade-Notes)

### Framework Integration

We have removed the abstracted framework integration interfaces classes that were in the following namespaces:
- `CloudCreativity\JsonApi\Contracts\Integration`
- `CloudCreativity\JsonApi\Integration`

These overcomplicated framework integration. Each integration package should write its own integration services
that integrate with how the specific framework delivers services. We see this as advantageous because you can
write the integration in the *style* of the framework you're integrating with.

### Error Handling

The underlying package removed the `ExceptionThrowerInterface` in favour of throwing JSON API exceptions,
and also removed the `RendererContainerInterface` in favour of handling the thrown JSON API exceptions directly.

The following classes have all been removed from this package as a result:

- `CloudCreativity\JsonApi\Exceptions\ErrorRenderer`
- `CloudCreativity\JsonApi\Exception\ExceptionThrower`
- `CloudCreativity\JsonApi\Exceptions\ExceptionThrower`
- `CloudCreativity\JsonApi\Exceptions\StandardRenderer`

Instead, your framework integration should use exception catching to cast the following exceptions to JSON-API
errors:

- `Neomerx\JsonApi\JsonApiException`
- `CloudCreativity\JsonApi\Error\ErrorException`
- `CloudCreativity\JsonApi\Error\MultiErrorException`

`CloudCreativity\JsonApi\Error\ThrowableError` was deprecated in `v0.4.0` and has now been removed. Please use
one of the exception classes above instead.

We have continued to use our own `Exception` implementations because the `neomerx/json-api` classes are not
sufficient for our requirements - particularly around constructing validation messages from our validator classes.

### HTTP Namespace

Note that the underlying package moved the `Parameters` and `Headers` namespaces into an `Http` namespace. You'll
need to update your `use` statements accordingly.
