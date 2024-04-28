# TOC - TOC library for document2

[![code-style](https://github.com/cable8mm/toc/actions/workflows/code-style.yml/badge.svg)](https://github.com/cable8mm/toc/actions/workflows/code-style.yml)
[![run-tests](https://github.com/cable8mm/toc/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cable8mm/toc/actions/workflows/run-tests.yml)
[![Packagist Version](https://img.shields.io/packagist/v/cable8mm/toc)](https://packagist.org/packages/cable8mm/toc)
[![Packagist Downloads](https://img.shields.io/packagist/dt/cable8mm/toc)](https://packagist.org/packages/cable8mm/toc/stats)
[![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/toc/php)](https://packagist.org/packages/cable8mm/toc)
[![Packagist Stars](https://img.shields.io/packagist/stars/cable8mm/toc)](https://github.com/cable8mm/toc/stargazers)
[![Packagist License](https://img.shields.io/packagist/l/cable8mm/toc)](https://github.com/cable8mm/toc/blob/main/LICENSE.md)

The TOC is a library for the project [document2](https://github.com/cable8mm/document2).

## Features

- [x] Laravel toc
- [x] Samsung Tizen toc
- [x] Naver clova ai toc
- [x] Rhymix toc

## Installation

```shell
composer require cable8mm/toc
```

## Usage

```php
namespace Cable8mm\Toc;

$markdown = '
- ## Prologue
    - [Release Notes](/docs/{{version}}/releases)
    - [Upgrade Guide](/docs/{{version}}/upgrade)
    - [Contribution Guide](/docs/{{version}}/contributions)
- ## Getting Started
    - [Installation](/docs/{{version}}/installation)
    - [Configuration](/docs/{{version}}/configuration)
';

$lines = Toc::of($markdown)->getLines();

foreach ($lines as $line) {
    // example "- ## Prologue"
    // example "    - [Release Notes](/docs/{{version}}/releases)"
    print $line->getTitle().PHP_EOL;
    //=> "Prologue"
    //=> "Release Notes"
    print $line->getLink().PHP_EOL;
    //=> null
    //=> "/docs/{{version}}/releases"
    print $line->getType().PHP_EOL;
    //=> ItemEnum::section
    //=> ItemEnum::page
    print $line->getDepth().PHP_EOL;
    //=> 1
    //=> 2
}
```

## Testing

```shell
composer test
```

## Formatting

```shell
composer lint
# Modify all files to comply with the PSR-12.

composer inspect
# Inspect all files to ensure compliance with PSR-12.
```

## License

The Document2 project is open-sourced software licensed under the [MIT license](LICENSE).
