# cloudcreativity/json-api

A framework agnostic implementation of the [jsonapi.org](http://jsonapi.org) spec. This repository extends
[neomerx/json-api](https://github.com/neomerx/json-api), adding in several additional features:

1. JSON API environment to obtain JSON API configuration for the current request.
2. Multiple schema sets loaded from a configuration array. Default schemas are merged with the schema set being loaded.
3. Build codec matchers from a configuration array.
4. HTML request body content validation.
5. Decoding request body content into standard objects, with a fluent interface for analysing content.

### Contributions

Contributions are absolutely welcome. Ideally submit a pull request, even more ideally with unit tests. Please note
the following:

* **Bug Fixes** - submit a pull request against the `master` branch.
* **Enhancements / New Features** - submit a pull request against the `develop` branch.

We'd recommend submitting an issue before taking the time to put together a pull request!

## 1. Environment Integration

Each JSON API request that is handled by an application has dependencies that (once resolved) remain constant for the
duration of the request. As different parts of your application - e.g. middleware, controllers, exception handlers -
all need to access these resolved dependencies, a standard interface is provided to obtain these dependencies.
This is similar to application frameworks providing access to singleton HTTP Request and Response objects.

The `CloudCreativity\JsonApi\Contracts\Integration\EnvironmentInterface` provides a standard interface to access
the current *state* of the JSON API request. The *state* comprises of:

* The url prefix for JSON API links.
* A single set of schemas.
* A single encoder instance.
* A decoder instance, if the request included a HTTP `Content-Type` header.
* JSON API request parameters.

> A concrete class (`CloudCreativity\JsonApi\Integration\EnvironmentService`) is provided to use if desired. This
expects to be initialised when JSON API support is required within an application.

> In middleware based applications (e.g. Laravel 5) we're using middleware on JSON API routes to initialise this
service.

> In event based applications (e.g. Zend 2), we're using an event listener to initialise the service once a JSON API
route has been matched.

> In either scenario, global middleware or a global event listener can be used to initialise the service if every route
in the application is a JSON API endpoint.

## 2. Schema Sets

Schemas can be loaded from configuration arrays, for example:

``` php
[
  'defaults' => [
    'Article' => 'ArticleSchema',
    'Post' => 'PostSchema',
    'Comment' => 'CommentSchema',
  ],
  'users' => [
    'User' => 'UserSchema',
  ],
  'tenant' => [
    'ArticleDashboard' => 'ArticleDashboardSchema',
  ],
]
```

If loaded into a `CloudCreativity\JsonApi\Repositories\SchemasRepository` instance, then your application will be
able to access either a default schema set, a `users` schema set or a `tenant` schema set. Both the `users` and `tenant`
schema sets will contain the default schemas as well as their own.

## 3. Codec Matcher Configuration

Codec matchers can be built from configuration arrays, for example:

``` php
[
  'encoders' => [
    // Encoder with no extra configuration.
    'application/vnd.api+json',
    // Encoder with the encoder options set to the supplied value.
    'application/json' => JSON_BIGINT_AS_STRING,
    // Encoder with both encoder options and depth set.
    'text/plain' => [
      'options' => JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
      'depth' => 123,
    ],
  ],
  'decoders' => [
    'application/vnd.api+json' => ObjectDecoder::class,
  ],
]
```

The above configuration will build a codec matcher if loaded into a
`CloudCreativity\JsonApi\Repositories\CodecMatcherRepository` instance. The URL prefix and schemas to use when creating
encoders are registered on the `CodecMatcherRepository` before creating a codec matcher.

## 4. HTML Request Body Content Validation

### Why?

PHP provides `json_decode` to decode a provided JSON string. However, the JSON API spec describes how the request
JSON should be *semantically* correct. E.g. when a client is providing a resource object for a create request, the
`attributes` member must be an object.

This package provides framework agnostic validation of the received request body content - so that it can be handled
knowing that not only has `json_decode` successfully run, but that the structure of the decoded JSON is as expected.
Provided decoders also returns decoded content as `StandardObjectInterface` instances, an object that provides a
number of helper methods for handling the decoded content e.g. within a controller.

### Let's see an example...

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

### Extensibility

Each validator is built on the concept that each expected member has its own validator, which can be overridden by any
object that implements `CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface`.

For example, a JSON-API resource object has `type`, `id`, `attributes` and `relationships` members. If you
want to use a custom validator to validate the `attributes` member, then that custom validator can be used on a
`ResourceObjectValidator` by calling `ResourceObjectValidator::setAttributesValidator()`.

Most frameworks implement their own validators. These framework validators can either be used after the HTTP body has
been decoder, or integrated into the decoding processing. To integrate, just wrap them in an object that implements
`CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface`.

## 5. Parsing to Standard Objects

The standard decoder, `DocumentDecoder` returns a `Document` instance. This provides methods to walk through the
JSON API content that was received by a server. All objects returned through the interface implement a
`StandardObjectInterface` and have methods for accessing sub-objects.

When combined with validation of incoming body content, controllers can walk through the data with confidence. If the
following JSON API data was received:

``` json
{
  "data": {
    "type": "article",
    "id": "123",
    "attributes": {
      "title": "Hello World",
      "content": "Check out jsonapi.org"
    },
    "relationships": {
      "author": {
        "data": {"type": "person", "id": "99"}
      }
    }
  }
}
```

The controller can do this:

``` php
class ArticleController
{

    use CloudCreativity\JsonApi\Helpers\DocumentProviderTrait;

    public function getValidator($id = null)
    {
       $validator = new ResourceObjectValidator('article', $id);

       $validator->attr('title', 'string')
         ->attr('content', 'string')
         ->belongsTo('author', 'person', ['required' => true]);

       return $validator;
    }

    public function updateAction($id)
    {
      $model = ... // get the article model.
      $validator = $this->getValidator($id);

      $resourceObject = $this
        ->getDocument($validator)
        ->getResourceObject();

      $model->fill($resourceObject
        ->getAttributes()
        ->toArray());

      $authorId = $resourceObject
        ->getRelationships()
        ->get('author')
        ->getData()
        ->getId();

      $author = ... // get the author model using `authorId`

      $model->setAuthor($author);

      // return response.
    }
}
```

## Status

This repository is under active development, but is anticipated to stabilise soon.

## License

Apache License (Version 2.0). Please see [License File](LICENSE) for more information.
