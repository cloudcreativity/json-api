# cloudcreativity/json-api

A framework agnostic implementation of the [jsonapi.org](http://jsonapi.org) spec. This repository extends
[neomerx/json-api](https://github.com/neomerx/json-api), adding in several additional features:

1. HTML request body content validation.
2. Parsing request body content into standard objects, with a fluent interface for analysing content.
3. Creating codec matchers (that contain encoders and decoders) from arrays.
4. Namespacing codec matchers, for when applications require different codec matchers for different parts of the
application. (E.g. running multiple separated APIs within the same web application.)

## 1. HTML Request Body Content Validation

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

## 2. Parsing to Standard Objects

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

## 3. Arrays to Codec Matchers

Configuration loading is a standard feature of PHP frameworks. This package provides a number of classes to enable the
loading of `Neomerx\JsonApi\Contracts\Codec\CodecMatcherInterface` objects from array configuration. This includes the
encoders and decoders that are contained within the `CodecMatcherInterface` object.

This is implemented using classes in the `CloudCreativity\JsonApi\Repositories` namespace. A `CodecMatchersRepository`
instance is injected with an `EncodersRepository` instance and a `DecodersRepository` instance.

In a basic example, the configuration would be:

``` php
return [
  // CodecMatchersRepository configuration.
  'codecMatcher' => [
    'encoders' => [
        'application/vnd.api+json',
        'application/vnd.api+json;charset=utf-8',
        'text/plain' => [
          'options' => 'humanized',
        ],
    ],
    'decoders' => [
        'application/vnd.api+json',
        'application/vnd.api+json;charset=utf-8',
    ],
  ],

  // EncodersRepository configuration
  'encoders' => [
    'options' => [
      'defaults' => [
        'options' => JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOTE,
        'depth' => 300,
      ],
      'humanized' => [
        'options' => JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
      ],
    ],
    'schemas' => [
      Article::class => ArticleSchema::class,
      Post::class => PostSchema::class,
    ],
  ],

  // DecodersRepository configuration
  'decoders' => [
     CloudCreativity\JsonApi\Decoders\DocumentDecoder::class,
  ]
];
```

## 4. Namespaced Codec Matchers

The basic configuration shown above can be extended to produce multiple namespaced codec matchers that assemble
different combinations of encoding options, schemas and decoders. We're using this for a complex application that has
multiple JSON APIs, each with different combinations of schemas but with common encoding/decoding options.

For example, the config above can become:

``` php
return [
  // CodecMatchersRepository configuration.
  'codecMatcher' => [
    'defaults' => [
      'encoders' => [
      ],
      'decoders' => [
        'application/vnd.api+json',
        'application/vnd.api+json;charset=utf-8',
      ],
    ],
    // Extends the defaults, adding in specific encoders
    'tenant-api' => [
      'encoders' => [
        'application/vnd.api+json' => [
          // No encoding options specified, so will use the default encoder options.
          'schemas' => 'tenant-schemas',
        ],
        'application/vnd.api+json;charset=utf-8' => [
          'schemas' => 'tenant-schemas',
        ],
      ],
    ],
    'user-api' => [
      'encoders' => [
        'application/vnd.api+json' => [
          'schemas' => 'user-schemas',
        ],
        'application/vnd.api+json' => [
          'schemas' => 'user-schemas',
        ],
        'text/plain' => [
          'schemas' => 'user-schemas',
          'options' => 'humanized',
        ],
      ],
    ],
  ],

  // EncodersRepository configuration
  'encoders' => [
    'options' => [
      'defaults' => [
        'options' => JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOTE,
        'depth' => 300,
      ],
      'humanized' => [
        'options' => JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
      ],
    ],
    'schemas' => [
      'defaults' => [
        Article::class => ArticleSchema::class,
        Post::class => PostSchema::class,
      ],
      // Schemas for the tenant api - add to the defaults
      'tenant-api' => [
        SalesReport::class => SalesReportSchema::class,
      ],
      // Schemas for the user api - add to the defaults
      'user-api' => [
        AccountSettings::class => AccountSettingsSchema::class,
      ],
    ],
  ],

  // DecodersRepository configuration
  'decoders' => [
     CloudCreativity\JsonApi\Decoders\DocumentDecoder::class,
  ],
];
```

The relevant codec matcher can be built for the relevant API routing group by calling `getCodecMatcher($name)` on the
`CodecMatchersRepository` instance (that we access via our service container).

## Status

This repository is under active development, but is anticipated to stabilise soon.

## License

Apache License (Version 2.0). Please see [License File](LICENSE) for more information.
