# Laravel Subresource Integrity

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![StyleCI](https://styleci.io/repos/119791861/shield?branch=master)](https://styleci.io/repos/119791861)
[![TravisCI](https://travis-ci.org/Elhebert/laravel-sri.svg?branch=master)](https://travis-ci.org/Elhebert/laravel-sri)

Small laravel 5.5+ package that'll generate the integrity hashes for your style and script files.

## Installation

```sh
$ composer require elhebert/laravel-sri
```

This package uses [auto-discovery](https://laravel.com/docs/5.5/packages#package-discovery), so you don't have to do anything. It works out of the box.

## Config

The base path of the assets will be the `public` directory. You can change it within the config file `subresource-integrity`.

You can also customize the hashing algorithm. Possible algorithms are `sha256`, `sha384` and `sha512`.

You can publish the config file using

```sh
$ php artisan vendor:publish --provider="Elhebert\SubresourceIntegrity\SriServiceProvider"
```

## Usage

To only get a hash, use `Sri::hash`:

```html
<link
    href="{{ asset('css/app.css') }}"
    rel="stylesheet"
    integrity="{{ Sri::hash('css/app.css') }}"
    crossorigin="anonymous"
>
```

To generate the HTML for the `integrity` and the `crossorigin` attributes, use `Sri::html`. It accepts two parameters:
- first one is the path;
- second one (default is `false`) tells if you want to pass the credentials when fetching the resource.

```html
<link
    href="{{ asset('css/app.css') }}"
    rel="stylesheet"
    {{ Sri::html('css/app.css') }}
>
```

### Blade directive

Two blade directive are available to make your views cleaner:

Use `@mixSri` to generate the `<link>` or `<script>` tag with the proper attributes and using the `mix()` helper to generate the asset path:
```php
@mixSri(string $path, bool $useCredentials = 'false')
```

Use `@assetSri` to generate the `<link>` or `<script>` tag with the proper attributes and using the `asset()` helper to generate the asset path:
```php
@assetSri(string $path, bool $useCredentials = 'false')
```

## Remote resources

This package also work for remote resources. Be careful that resources like Google Fonts [won't work](https://github.com/google/fonts/issues/473).

```html
<script
    src="http://code.jquery.com/jquery-3.3.1.min.js"
    integrity="{{ Sri::hash('http://code.jquery.com/jquery-3.3.1.min.js') }}"
    crossorigin="anonymous"
></script>
```

You can also use the blade directives for remotes resources. Both are similar for external assets. It'll simply load the asset without the laravel helper.

```php
@mixSri('http://code.jquery.com/jquery-3.3.1.min.js')
@assetSri('http://code.jquery.com/jquery-3.3.1.min.js')
```

will both generate the equivalent of the`<script>` tag just above.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for more details.

## License

This project and the Laravel framework are open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
