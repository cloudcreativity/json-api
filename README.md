# cloudcreativity/json-api

This repository extends [neomerx/json-api](https://github.com/neomerx/json-api), adding in several additional
framework-agnostic features:

1. Multiple schema sets loaded from a configuration array. Default schemas are merged with the schema set being loaded.
2. Build codec matchers from a configuration array.
3. HTML request body content validation.
4. Decoding request body content into standard objects, with a fluent interface for analysing content.

More information on each feature below.

### Status

This repository is under active development, but is in use in production applications.

Note that we've made substantial breaking changes between `v0.4` and `v0.5`. This is the last major reworking of this
package prior to `v1.0`. We felt it was advantageous to do the rewrite because the changes reflect the use of this
package in production environments and we believe `v0.5` is a substantial improvement. Any changes prior to `v1.0`
will be kept as minimal as possible and we'll document how to upgrade in the [Upgrade Guide](UPGRADE.md)
 
Note that we would like to get this package to `v1.0` as soon as possible. However, this package is 
dependent on `neomerx/json-api` which is not yet at `v1.0`. We will issue `v1.0` when `neomerx/json-api` is tagged
as `v1.0`.

### License

Apache License (Version 2.0). Please see [License File](LICENSE) for more information.

### Contributions

Contributions are absolutely welcome. Ideally submit a pull request, even more ideally with unit tests. Please note
the following:

* **Bug Fixes** - submit a pull request against the `master` branch.
* **Enhancements / New Features** - submit a pull request against the `develop` branch.

We'd recommend submitting an issue before taking the time to put together a pull request!

## 1. Schema Sets

Schemas can be loaded from configuration arrays, for example:

``` php
[
  'defaults' => [
    Article::class => ArticleSchema::class,
    Post::class => PostSchema::class,
    Comment::class => CommentSchema::class,
  ],
  'users' => [
    User::class => UserSchema::class,
  ],
  'tenant' => [
    ArticleDashboard::class => ArticleDashboardSchema::class,
  ],
]
```

If loaded into a `CloudCreativity\JsonApi\Repositories\SchemasRepository` instance, then your application will be
able to access either a default schema set, a `users` schema set or a `tenant` schema set. Both the `users` and `tenant`
schema sets will contain the default schemas as well as their own.

This is useful if your application exposes multiple APIs, with different schema sets for each API.

## 2. Codec Matcher Configuration

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
    // Decoder that uses our default DocumentDecoder class
    'application/vnd.api+json',
    // Decoder with a specified decoder
    'text/plain' => MyCustomDecoder::class,
  ],
]
```

The above configuration will build a codec matcher if loaded into a
`CloudCreativity\JsonApi\Repositories\CodecMatcherRepository` instance. The URL prefix and schemas to use when creating
encoders are registered on the `CodecMatcherRepository` before creating a codec matcher.

## 3. HTML Request Body Content Validation

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
controller. The controller is injected with a `ValidatorProviderInterface` instance that provides the validators
for the `article` resource:

``` php
<?php

use CloudCreativity\JsonApi\Contracts\Object\StandardObjectInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidatorProviderInterface;
use CloudCreativity\JsonApi\Decoders\DocumentDecoder;
use CloudCreativity\JsonApi\Exceptions\ValidationException;

class ArticleController
{

  private $validators
  
  public function __construct(ValidatorProviderInterface $articleValidators)
  {
    $this->validators = $articleValidators;
  }

  public function createAction()
  {
    $document = $this->getDocument();
    $validator = $this->validators->createResource();
    
    if (!$validator->isValid($document)) {
      // your Exception handler can turn this into a JSON API error response...      
      throw new ValidationException($validator->getErrors());
    }
    
    /** @var StandardObjectInterface $attributes */
    // this interface has lots of helper methods for handling the data
    $attributes = $document->getResource()->getAttributes();
  }
  
  private function getDocument()
  {
    $content = '...' // get HTTP request body content
    $decoder = new DocumentDecoder();
    
    return $decoder->decode($content);
  }
}

```

In this refactored controller, `$data` can be used knowing that it has passed validation of the JSON API spec, and has
been cast to an instance of `CloudCreativity\JsonApi\Object\Document\Document`, providing a fluid interface for
handling the input within the controller.

If the provided input did not pass validation, then the decoder throws a
`CloudCreativity\JsonApi\Error\MultiErrorException` which contains the JSON API error messages indicating what is
invalid, including JSON pointers to the source of the validation error.

### Validator Providers

The approach is that each JSON API resource type should have its own instance of the `ValidatorProviderInterface`.
When creating these validator providers, you should inject them with a validator factory instance - 
this package provides one as `CloudCreativity\JsonApi\Validators\ValidatorFactory`. The factory allows you to 
construct the default validators that are provided by this package.

### Extensibility

Validators are highly extensible. The validation concept is that there is a validator interface for each 'leaf' of 
the document JSON that is expected. Validator interface can be found in the 
`CloudCreativity\JsonApi\Contracts\Validators` namespace. 

A lot of frameworks provide their own validators. In our Laravel extension package, we've used the Laravel validators
to validate the attributes member of a JSON API resource. All we had to do was wrap a Laravel validator in the 
attributes validator interface provided by this package.

## 4. Parsing to Standard Objects

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

This can be used in the controller example above:

``` php
class ArticleController
{

    // ...

    public function updateAction($id)
    {
      $model = Article::find($id); // ... get the article model.
      $document = $this->getDocument();
      $validator = $this->validators->updateResource($model, $id);
      
      if (!$validator->isValid($document)) {      
        throw new ValidationException($validator->getErrors());
      }
      
      $attributes = $document
        ->getResource()
        ->getAttributes()
        // this method only gets these keys if they exist, which is great for patch requests
        ->getMany(['title', 'content', 'sub-title']);

      $model->fill($attributes);

      $authorId = $document
        ->getResource()
        ->getRelationships()
        ->getRelationship('author')
        ->getIdentifier()
        ->getId();

      $author = Author::find($authorId); // get the author model using `authorId`
      $model->setAuthor($author);
      $model->save();
      
      // return response.
    }
    
    // ...
}
```
