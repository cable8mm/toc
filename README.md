# TOC - Table of Contents Parser for Markdown

[![code-style](https://github.com/cable8mm/toc/actions/workflows/code-style.yml/badge.svg)](https://github.com/cable8mm/toc/actions/workflows/code-style.yml)
[![run-tests](https://github.com/cable8mm/toc/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cable8mm/toc/actions/workflows/run-tests.yml)
[![Packagist Version](https://img.shields.io/packagist/v/cable8mm/toc)](https://packagist.org/packages/cable8mm/toc)
[![Packagist Downloads](https://img.shields.io/packagist/dt/cable8mm/toc)](https://packagist.org/packages/cable8mm/toc/stats)
[![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/toc/php)](https://packagist.org/packages/cable8mm/toc)
[![Packagist Stars](https://img.shields.io/packagist/stars/cable8mm/toc)](https://github.com/cable8mm/toc/stargazers)
[![Packagist License](https://img.shields.io/packagist/l/cable8mm/toc)](https://github.com/cable8mm/toc/blob/main/LICENSE)

**TOC** parses Markdown documents and extracts table of contents (navigation) structures. It supports multiple documentation formats including Laravel, Samsung Tizen, Naver Clova AI, and Rhymix.

Originally built for the [document2](https://github.com/cable8mm/document2) project.

---

## Installation

```shell
composer require cable8mm/toc
```

---

## Quick Start

Parse a Markdown TOC and iterate over its items:

```php
use Cable8mm\Toc\Toc;

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
    echo $line->getTitle().PHP_EOL;      // "Prologue", "Release Notes", ...
    echo $line->getLink().PHP_EOL;       // null, "/docs/{{version}}/releases", ...
    echo $line->getType()->name.PHP_EOL; // "section", "page", ...
    echo $line->getDepth().PHP_EOL;      // 1, 2, ...
}
```

---

## Supported Document Styles

### Laravel Style (`- ## Section`, indented `- [Page](link)`)

```markdown
- ## Prologue
  - [Release Notes](/docs/{{version}}/releases)
  - [Upgrade Guide](/docs/{{version}}/upgrade)
```

### Samsung Tizen Style (`# Section`, `## [Page](link)`)

```markdown
# What is Tizen?

## Versions

## [Overview](/platform/what-is-tizen/overview.md)

### [TV](/platform/what-is-tizen/profiles/tv.md)
```

### Naver Clova AI Style (`# Section`, `* [Page](link)` with 2-space indent)

```markdown
# Summary

## Clova Face Recognition

- [CFR API란?](/CFR/API_Guide.md#Overview)
  - [유명인 얼굴 인식 API](/CFR/API_Guide.md#CelebrityAPI)
```

### Rhymix Style (`### Section`, `- [Page](link)`)

```markdown
### 개요

- [설치 환경](./ko/introduction/requirements.md)
- [라이믹스 설치](./ko/introduction/install.md)
```

---

## API Reference

### `Toc` — Main parser class

| Method                                | Description                                     | Returns   |
| ------------------------------------- | ----------------------------------------------- | --------- |
| `Toc::of(string $markdown)`           | Create and parse markdown in one call (factory) | `Toc`     |
| `new Toc(string $markdown)`           | Create instance (call `->of()` to parse)        | `Toc`     |
| `getLines()`                          | Get all parsed line items                       | `Item[]`  |
| `getLine(int $n)`                     | Get the nth line item (0-based)                 | `Item`    |
| `getSectionTitle(string $pageTitle)`  | Find the section title containing a page        | `?string` |
| `toArray()`                           | Get grouped sections with their pages           | `array`   |
| `addConverters(ConverterInterface[])` | Add custom preprocessors                        | `static`  |

**Example — find which section a page belongs to:**

```php
$toc = Toc::of($markdown);
echo $toc->getSectionTitle('Release Notes'); // "Prologue"
echo $toc->getSectionTitle('Installation');  // "Getting Started"
```

**Example — grouped navigation structure:**

```php
$sections = Toc::of($markdown)->toArray();

foreach ($sections as $section) {
    echo $section['section']->getTitle().PHP_EOL; // Section heading
    foreach ($section['pages'] as $page) {
        echo '  - '.$page->getTitle().PHP_EOL;    // Page under this section
    }
}
```

### `Item` — A single TOC line

| Method       | Description                        | Returns    |
| ------------ | ---------------------------------- | ---------- |
| `getTitle()` | Extract the display title          | `string`   |
| `getLink()`  | Extract the URL (if it's a link)   | `?string`  |
| `getType()`  | Whether it's a `section` or `page` | `ItemEnum` |
| `getDepth()` | Nesting level (1-based)            | `int`      |
| `toHtml()`   | Render as `<li>` HTML tag          | `string`   |

**Example — render as HTML:**

```php
$lines = Toc::of($markdown)->getLines();

foreach ($lines as $line) {
    echo $line->toHtml().PHP_EOL;
    // <li><h2>Prologue</h2></li>
    // <li><a href="/docs/{{version}}/releases">Release Notes</a></li>
}
```

### `ItemEnum` — Item type enum

| Case                | Meaning                     |
| ------------------- | --------------------------- |
| `ItemEnum::section` | A section heading (no link) |
| `ItemEnum::page`    | A page entry (has a link)   |

---

## Advanced Usage

### Custom depth calculation

Different documentation styles use different indent patterns. Pass style-specific parameters to `getDepth()`:

```php
// Tizen style — uses '#' headings with '#' symbol
Item::of('## Versions')->getDepth(indent: '#', symbol: '#', initialHCount: 1, depth: 1);
// => 2

// Naver Clova style — uses '*' symbol with 2-space indent
Item::of('* [CFR API란?](/CFR/API_Guide.md#Overview)')
    ->getDepth(indent: '  ', symbol: '*', initialHCount: 1, depth: 2);
// => 3

// Rhymix style — starts from h3
Item::of('- [설치 환경](./ko/introduction/requirements.md)')
    ->getDepth(symbol: '-', initialHCount: 3, depth: 1);
// => 2
```

### Custom converters

Add preprocessors to clean or transform markdown before parsing:

```php
use Cable8mm\Toc\Converters\CleanJustTopHConverter;

$toc = new Toc($markdown);
$toc->addConverters([
    new CleanJustTopHConverter, // Removes standalone top-level # headings
]);

// Now parse
$reflection = new ReflectionMethod(Toc::class, 'normalize');
$reflection->setAccessible(true);
$reflection->invoke($toc);
```

You can also implement your own converter by implementing `ConverterInterface`:

```php
use Cable8mm\Toc\Contracts\ConverterInterface;
use Cable8mm\Toc\Types\MarkdownString;

class MyConverter implements ConverterInterface
{
    public function do(MarkdownString $markdown): MarkdownString
    {
        // Transform $markdown, then return a new MarkdownString
        $cleaned = some_transformation((string) $markdown);
        return new MarkdownString($cleaned);
    }
}
```

---

## Testing

```shell
composer test
```

---

## Code Style

```shell
composer lint    # Auto-fix PSR-12 issues
composer inspect # Check PSR-12 compliance
```

---

## License

The MIT License (MIT). See [LICENSE](LICENSE).
