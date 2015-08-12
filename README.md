## Description

Extension for [neomerx/json-api](https://github.com/neomerx/json-api) that adds HTML Request Body content validation.

## Why?

PHP provides `json_decode` to decode a provided JSON string. However, the JSON API spec describes how the request
JSON should be *semantically* correct. E.g. when a client is providing a resource object for a create request, the
`attributes` member must be an object.

This package provides framework agnostic validation of the received request body content - so that it can be handled
knowing that not only has `json_decode` successfully run, but that the structure of the decoded JSON is as expected. 
Provided decoders also returns decoded content as `StandardObject` instances, an object that provides a number of 
helper methods for handling the decoded content e.g. within a controller.

## Status

This repository is under active development and is currently in a pre-release state.

## License

Apache License (Version 2.0). Please see [License File](LICENSE) for more information.
