[![Build Status](https://travis-ci.org/cloudcreativity/json-api.svg?branch=master)](https://travis-ci.org/cloudcreativity/json-api)

# cloudcreativity/json-api

This repository extends [neomerx/json-api](https://github.com/neomerx/json-api), adding in several additional
framework-agnostic features. These are:

1. **Apis**: JSON API configuration on a per-API basis, to support applications having many JSON APIs (e.g. version
controlled for public facing APIs) each with different settings.
2. **Store**: Maps JSON API identifiers to specific models/entities, and translates query parameters into 
queries for models/entities.
3. **Hydrators**: Handles the transfer of data from JSON API resources representations into models/entities. 
4. **Validators**: A full suite of validators for validating JSON API documents (HTTP request body content) and 
returning JSON API errors.
5. **Errors**: Mutable errors allow JSON API errors to be added to via your application stack and construction from
arrays.
6. **Testing**: Test helpers to assert content within a JSON API document.

For a framework specific implementation, see 
[cloudcreativity/laravel-json-api](https://github.com/cloudcreativity/laravel-json-api)

### Status

This repository is under active development, but is in use in production applications. We are currently on a path
to `v1.0.0` and should hit that soon.

### License

Apache License (Version 2.0). Please see [License File](LICENSE) for more information.

### Contributions

Contributions are absolutely welcome. Ideally submit a pull request, even more ideally with unit tests. Please note
the following:

* **Bug Fixes** - submit a pull request against the `master` branch.
* **Enhancements / New Features** - submit a pull request against the `develop` branch.

We'd recommend submitting an issue before taking the time to put together a pull request!
