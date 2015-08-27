## Description

Extension for [neomerx/json-api](https://github.com/neomerx/json-api) that adds HTML Request Body content validation.

## Why?

PHP provides `json_decode` to decode a provided JSON string. However, the JSON API spec describes how the request
JSON should be *semantically* correct. E.g. when a client is providing a resource object for a create request, the
`attributes` member must be an object.

This package provides framework agnostic validation of the received request body content - so that it can be handled
knowing that not only has `json_decode` successfully run, but that the structure of the decoded JSON is as expected.
Provided decoders also returns decoded content as `StandardObjectInterface` instances, an object that provides a
number of helper methods for handling the decoded content e.g. within a controller.

## Let's see an example...

A common scenario would be to decode received input for a resource object and then attempt to access its supplied
attributes within a controller. For example:

``` php
<?php

class ArticleController
{

  public function createAction()
  {
      $content = ... // get HTTP request body content.
      $data = json_decode($content, true);
      $attributes = $data['data']['attributes'];
  }
}
```

This is unsafe because the controller is assuming that the decoded `$data` is an array, has a `data` key, which
itself is an array with an `attributes` key.  This cannot be assumed because a client request cannot be trusted.

Also, the JSON-API spec specifies certain errors that must be sent if the provided input for a create resource under
certain scenarios - e.g.:

> A server MUST return `409 Conflict` when processing a `POST` request in which the resource object's `type` is not among
the type(s) that constitute the collection represented by the endpoint.

However, the controller has not checked whether `$data['type']` exists or whether it is an expected type.

The above example can be refactored to use validators to parse the provided content before handling it within the
controller:

``` php
<?php

use CloudCreativity\JsonApi\Validator\Resource\ResourceObjectValidator;
use CloudCreativity\JsonApi\Validator\Document\DocumentValidator;
use CloudCreativity\JsonApi\Decoders\DocumentDecoder;

class ArticleController
{

  public function getDecoder()
  {
    // We provide a ResourceObjectValidator so that the data member in the
    // document is validated as a resource object.
    $validator = new DocumentValidator(new ResourceObjectValidator('article'));
    return new DocumentDecoder($validator);
  }

  public function createAction()
  {
    $content = ... // get HTTP request body content.
    /** @var CloudCreativity\JsonApi\Object\Document\Document $data */
    $data = $this->getDecoder()->decode($content);
    $attributes = $data->getResourceObject()->getAttributes();
  }
}

```

In this refactored controller, `$data` can be used knowing that it has passed validation of the JSON API spec, and has
been cast to an instance of `CloudCreativity\JsonApi\Object\Document\Document`, providing a fluid interface for
handling the input within the controller.

If the provided input did not pass validation, then the decoder throws a
`CloudCreativity\JsonApi\Error\MultiErrorException` which contains the JSON API error messages indicating what is
invalid, including JSON pointers to the source of the validation error.

## Extensibility

Each validator is built on the concept that each expected member has its own validator, which can be overridden by any
object that implements `CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface`.

For example, a JSON-API resource object has `type`, `id`, `attributes` and `relationships` members. If you
want to use a custom validator to validate the `attributes` member, then that custom validator can be used on a
`ResourceObjectValidator` by calling `ResourceObjectValidator::setAttributesValidator()`.

## Status

This repository is under active development and is currently in a pre-release state.

## License

Apache License (Version 2.0). Please see [License File](LICENSE) for more information.
