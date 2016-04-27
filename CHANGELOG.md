# Change Log
All notable changes to this project will be documented in this file. This project adheres to
[Semantic Versioning](http://semver.org/) and [this changelog format](http://keepachangelog.com/).

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
