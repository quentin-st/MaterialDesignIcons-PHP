# MaterialDesign-PHP

Painlessly drive away from using the MaterialDesignIcons webfont!

[![Latest Stable Version](https://poser.pugx.org/mesavolt/mdi-php/v/stable)](https://packagist.org/packages/mesavolt/mdi-php)
[![Build Status](https://travis-ci.org/chteuchteu/MaterialDesignIcons-PHP.svg)](https://travis-ci.org/chteuchteu/MaterialDesignIcons-PHP)
[![Coverage Status](https://coveralls.io/repos/github/chteuchteu/MaterialDesignIcons-PHP/badge.svg)](https://coveralls.io/github/chteuchteu/MaterialDesignIcons-PHP)
[![License](https://poser.pugx.org/mesavolt/mdi-php/license)](https://packagist.org/packages/mesavolt/mdi-php)

## Installation

Add this package to your project:

```bash
composer require mesavolt/mdi-php
```

### Using `@mdi/svg` npm package

Add `@mdi/svg` npm package to your project:

```bash
yarn add @mdi/svg

# or, using npm
npm install @mdi/svg
```

Icons location will be automatically detected.

### Other

If you didn't install the icons pack using one of the documentation methods
above, you can globally configure the icons location. This should be done once and before
the first usage of the `Mdi::mdi` function.

```php
Mdi::withIconsPath(__DIR__.'/../../../node_modules/@mdi/svg/svg/');
```

## Usage

Use it in your views:

```php
<button>
    <?php echo Mdi::mdi('account'); ?>
    My account
</button>
```

The `mdi` function provides 4 arguments to customize its output:

 - the icon (you can provide either `account`, `mdi-account` or `mdi mdi-account`)
 - its class (`fill-muted` for instance)
 - its size (defaults to 24px)
 - some more attributes that will be added to the `<svg>` tag (`['aria-label' => 'My account']` for instance)

### Default attributes

You can add custom default attributes, or edit and remove the provided defaults.

| Attribute name | Default value                                  |
|----------------|------------------------------------------------|
| `viewBox`      | `0 0 24 24`                                    |
| `xmlns`        | `http://www.w3.org/2000/svg`                   |
| `width`        | Whatever size was specified (defaults to `24`) |
| `height`       | Whatever size was specified (defaults to `24`) |
| `role`         | `presentation`                                 |

```php
Mdi::withDefaultAttributes([
    'data-toggle' => 'tooltip',     // Add a new one
    'role' => 'img',                // Replace default `presentation` value with `img`
    'xmlns' => null,                // Remove default `xmlns` attribute
]);
```

### Plugging it to Twig

Simply register `Mdi::mdi` as a Twig simple function and get started!

```php
use Twig\TwigFunction;

$twigEnv->addFunction(new TwigFunction('mdi', [Mdi::class, 'mdi'], ['is_safe' => ['html']]));
```

```twig
<button>
    {{ mdi('account', 'fill-muted', 42, {'aria-label': 'My account icon'}) }}
    My account
</button>
```
