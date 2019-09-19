# Laravel Subresource Integrity

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![StyleCI](https://styleci.io/repos/119791861/shield?branch=master)](https://styleci.io/repos/119791861)
[![TravisCI](https://travis-ci.org/Elhebert/laravel-sri.svg?branch=master)](https://travis-ci.org/Elhebert/laravel-sri)

Small Laravel 6+ package that'll generate the integrity hashes for your style and script files.

For Laravel 5.5+ support, use the [v1 branch](https://github.com/Elhebert/laravel-sri/tree/v1).

## About Subresources Integrity

From [MDN](https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity):
> Subresource Integrity (SRI) is a security feature that enables browsers to verify that files they fetch (for example, from a CDN) are delivered without unexpected manipulation. It works by allowing you to provide a cryptographic hash that a fetched file must match.

Troy Hunt wrote an article speaking on the subject, you can read it [here](https://www.troyhunt.com/protecting-your-embedded-content-with-subresource-integrity-sri/)

## Installation

```sh
$ composer require elhebert/laravel-sri
```

This package uses [auto-discovery](https://laravel.com/docs/5.5/packages#package-discovery), so you don't have to do anything. It works out of the box.

## Config

If you want ot make changes in the configuration you can publish the config file using

```sh
$ php artisan vendor:publish --provider="Elhebert\SubresourceIntegrity\SriServiceProvider"
```

### Content of the configuration

| key | default value | possible values |
| - | - | - |
| base_path | `base_path('/public')` | |
| algorithm | sha256 | sha256, sha384 and sha512 |
| hashes | `[]` | (see "[How to get a hash](#how-to-get-a-hash)) |
| mix_sri_path | `public_path('mix-sri.json')` | (see "[How to get a hash](#how-to-get-a-hash)) |

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

## How to get a hash

### Store hashes in the configuration

You can references the assets in the configuration like this:

```php
[
    // ...

    'hashes' => [
        'css/app.css' => 'my_super_hash'
        'https://code.jquery.com/jquery-3.3.1.min.js' => 'sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8='
    ]
]
```

This means, you have to calculate the hashes yourself. To do this, you can use [report-uri.io](https://report-uri.com/home/sri_hash), [mozilla hash generator](https://www.srihash.org/) or any other resource available.

### Using a webpack (or Mix) plugin to generate hashes on build

It expect a `mix-sri.json` file with a similar structure to the `mix-manifest.json`:
```json
{
    "/css/app.css": "my_super_hash",
    "/js/app.js": "my_super_hash",
}
```

The filename and path can be changed in the configuration at any time.

> Self promotion: I made a Laravel Mix extension [laravel-mix-sri](https://github.com/Elhebert/laravel-mix-sri) for this purpose.

### Generate them on the fly

If it can't find the asset hash in the config file nor in the mix-sri.json file, it'll generate the hash on each reload of the page.

This method is the least recommended, because it reduce performance and make your page load slower.

## Remote resources

This package also work for remote resources. Be careful that resources like Google Fonts [won't work](https://github.com/google/fonts/issues/473).

```html
<script
    src="http://code.jquery.com/jquery-3.3.1.min.js"
    integrity="{{ Sri::hash('http://code.jquery.com/jquery-3.3.1.min.js') }}"
    crossorigin="anonymous"
></script>
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for more details.

## License

This project and the Laravel framework are open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
