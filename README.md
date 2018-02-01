# Laravel Subresource Integrity

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

Small package that'll generate the integrity hashes for your style and script file.

## Installation

```sh
$ composer require elhebert/laravel-sri
```

This package use auto-discovery, so you don't have to do anything. It works out of the box.

## Config

The base path of the assets will be the `public` directory. You can change it within the config file `subresource-integrity`.

You can publish the config file using

```sh
$ php artisan vendor:publish --provider="Elhebert\SubresourceIntegrity\SriServiceProvider"
```

## Usage

To only get a hash use `Sri::hash`

```html
<link
    href="{{ asset('css/app.css') }}"
    rel="stylesheet"
    integrity="{{ Sri::hash('css/app.css') }}"
    crossorigin="anonymous"
>
```

To generate the HTML for the `integrity` and the `crossorigin` attributes, use `Sri::html` or `Sri::withCredentials`.

`Sri::html` accepts two parameters:
- first one is the path;
- second one (optional, default is `false`) tells if you want to pass the credentials when fetching the resource.

`Sri::withCredentials` is an alias for `Sri::html('your-resource.js', true)`.

```html
<link
    href="{{ asset('css/app.css') }}"
    rel="stylesheet"
    {{ Sri::html('css/app.css') }}
>
```

This package also work for remote resources. Be careful that resources like Google Fonts [won't work](https://github.com/google/fonts/issues/473).
So:
- `{{ Sri::html('css/app.css') }}` generates `integrity="sha-xxx…" crossorigin="anonymous"`;
- `{{ Sri::html('css/app.css', true) }}` generates `integrity="sha-xxx…" crossorigin="use-credentials"`;
- `{{ Sri::withCredentials('css/app.css') }}` is a verbose and thus more explicit alias for the previous line.

```html
<script
    src="http://code.jquery.com/jquery-3.3.1.min.js"
    {{ Sri::html('http://code.jquery.com/jquery-3.3.1.min.js') }}
></script>
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for more details.

## License

This project and The Laravel framework are open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
