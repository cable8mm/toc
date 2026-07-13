# TOC Library — AI Agent Guide

## Overview

`cable8mm/toc` is a PHP library that parses Markdown documents and extracts table of contents (navigation) structures. It supports multiple documentation formats (Laravel, Samsung Tizen, Naver Clova AI, Rhymix) and provides a clean, extensible architecture.

**Package**: `cable8mm/toc`  
**PHP Requirement**: ^8.2  
**License**: MIT  
**Repository**: https://github.com/cable8mm/toc

---

## Architecture

### Pipeline

```
Toc::of(string $markdown)
  ├── normalize()   → ConverterInterface[] pipeline (preprocess markdown)
  ├── mapping()     → explode(PHP_EOL) + Item::of() for each line
  └── grouping()    → classify lines as section/page, build nested structure
```

### Namespace Structure

```
Cable8mm\Toc\
├── Toc.php                    # Main parser class (facade + pipeline orchestrator)
├── Item.php                   # Single TOC line representation
├── Contracts\
│   ├── ConverterInterface.php # Markdown preprocessor contract
│   └── ItemInterface.php      # TOC item contract
├── Converters\
│   ├── CleanConverter.php     # Default: removes non-TOC lines
│   └── CleanJustTopHConverter.php  # Optional: removes standalone top-level heading
├── Enums\
│   └── ItemEnum.php           # section | page
└── Types\
    └── MarkdownString.php     # Value object wrapping a markdown string
```

---

## Key Classes

### `Toc` — Main Parser

**File**: `src/Toc.php`

The central class. It accepts a raw Markdown string, runs it through a converter pipeline, parses each line into an `Item`, and groups items into sections with pages.

**Public API**:

| Method              | Signature                                     | Description                                                                |
| ------------------- | --------------------------------------------- | -------------------------------------------------------------------------- |
| `of()`              | `static of(string $markdown): static`         | Factory: creates + parses in one call                                      |
| `__construct()`     | `new Toc(string $markdown)`                   | Creates instance (call `of()` to parse)                                    |
| `getLines()`        | `getLines(): Item[]`                          | Returns all parsed line items                                              |
| `getLine()`         | `getLine(int $n): Item`                       | Returns nth line (0-based), throws `InvalidArgumentException` if not found |
| `getSectionTitle()` | `getSectionTitle(string $pageTitle): ?string` | Finds the section title containing a given page title                      |
| `toArray()`         | `toArray(): array`                            | Returns grouped structure: `[['section' => Item, 'pages' => Item[]], ...]` |
| `addConverters()`   | `addConverters(ConverterInterface[]): static` | Appends converters to the pipeline                                         |
| `__toString()`      | `__toString(): string`                        | Returns the normalized markdown string                                     |

**Internal methods** (protected, called by `of()`):

| Method        | Description                                                                                      |
| ------------- | ------------------------------------------------------------------------------------------------ |
| `normalize()` | Iterates over `$this->converters`, applies each `ConverterInterface::do()`                       |
| `mapping()`   | Explodes markdown by `PHP_EOL`, maps each line to `Item::of()`                                   |
| `grouping()`  | Iterates lines, classifies as `section` (no link) or `page` (has link), builds `$this->sections` |

**Default converter**: `CleanConverter` is registered in the constructor.

**Important implementation details**:

- `normalize()` uses `foreach` (not `array_map`) to apply converters sequentially
- `getSectionTitle()` calls `$page->getTitle()` (not `$page->title` — that property does not exist)
- `getLine()` throws `InvalidArgumentException` for out-of-bounds indices

---

### `Item` — Single TOC Line

**File**: `src/Item.php`

Represents one line from the parsed TOC. Can be either a **section** (heading without a link) or a **page** (entry with a link).

**Public API**:

| Method         | Signature                             | Description                                     |
| -------------- | ------------------------------------- | ----------------------------------------------- |
| `of()`         | `static of(string $markdown): static` | Factory                                         |
| `getTitle()`   | `getTitle(): string`                  | Extracts display title from markdown            |
| `getLink()`    | `getLink(): ?string`                  | Extracts URL from `[text](url)` syntax, or null |
| `getType()`    | `getType(): ItemEnum`                 | Returns `ItemEnum::section` or `ItemEnum::page` |
| `getDepth()`   | `getDepth(...): int`                  | Calculates nesting depth (see below)            |
| `toHtml()`     | `toHtml(): string`                    | Renders as `<li>` HTML tag                      |
| `__toString()` | `__toString(): string`                | Alias for `toHtml()`                            |

**`getTitle()` logic**:

1. If markdown contains `[text](url)`, extract `text` from the link syntax
2. Otherwise, match `^[#\s\-]+([^\[\]]+)$` and return the captured group
3. If neither matches, throws `LogicException` via `assert()`

**`getType()` logic**:

- Delegates to `MarkdownString::hasLink()`
- If the markdown contains a link → `ItemEnum::page`
- Otherwise → `ItemEnum::section`

**`getDepth()` parameters** (all nullable with defaults):

| Parameter             | Default             | Description                                                  |
| --------------------- | ------------------- | ------------------------------------------------------------ |
| `$indent`             | `'    '` (4 spaces) | Indentation string for the style                             |
| `$symbol`             | `'-'`               | List marker symbol (`-`, `*`, `#`)                           |
| `$depth`              | `0`                 | Base depth offset                                            |
| `$initialHCount`      | `0`                 | Number of `#` characters to subtract for heading-based depth |
| `$initialIndentCount` | `0`                 | (Unused in current implementation)                           |

**Depth calculation algorithm**:

1. Count `#` characters at start of line → `$hTagCount`
2. `$baseDepth = max(0, $hTagCount - $initialHCount)`
3. If `$hTagCount > 0`, reset `$depth = 0`
4. Replace `#` heading with `$symbol` for consistent matching
5. Match against indentation patterns:
   - No indent → `$depth + 1 + $baseDepth`
   - 1 indent → `$depth + 2 + $baseDepth`
   - 1 tab / 1 indent (no space after) → `$depth + 3 + $baseDepth`
   - 2 tabs / 2 indents → `$depth + 4 + $baseDepth`
6. If no pattern matches, throws `InvalidArgumentException`

**`toHtml()` output**:

- Section: `<li><h2>{title}</h2></li>`
- Page with link: `<li><a href="{url}">{title}</a></li>`
- Page without link: `<li>{title}</li>`

---

### `MarkdownString` — Value Object

**File**: `src/Types/MarkdownString.php`

A simple string wrapper that provides link detection utilities.

| Method         | Description                                     |
| -------------- | ----------------------------------------------- |
| `getLink()`    | Extracts URL from `[text](url)` syntax, or null |
| `hasLink()`    | Returns `true` if `getLink()` is not null       |
| `__toString()` | Returns the raw markdown string                 |

---

### `ItemEnum` — Type Enum

**File**: `src/Enums/ItemEnum.php`

```php
enum ItemEnum
{
    case section;  // A heading without a link (e.g., "- ## Prologue")
    case page;     // An entry with a link (e.g., "- [Page](/url)")
}
```

---

### `ConverterInterface` — Extension Point

**File**: `src/Contracts/ConverterInterface.php`

```php
interface ConverterInterface
{
    public function do(MarkdownString $toc): MarkdownString;
}
```

Implement this to create custom markdown preprocessors. The `do()` method receives the current markdown and must return a new `MarkdownString`.

---

## Converters

### `CleanConverter` (default)

**File**: `src/Converters/CleanConverter.php`

Filters out lines that are not TOC entries. Keeps only lines matching:

- `^- ` (list items starting with `-`)
- `^\s+- ` (indented list items)
- `^#+` (heading lines)

### `CleanJustTopHConverter` (optional)

**File**: `src/Converters/CleanJustTopHConverter.php`

Removes a standalone top-level heading (the heading level with the fewest `#` characters) if it appears exactly once. This handles cases like a document title that shouldn't appear in the TOC.

**Algorithm**:

1. Scan all lines, find the minimum `#` count among non-empty heading lines
2. Count how many lines use that heading level
3. If exactly one line uses that level → remove it
4. Otherwise → return markdown unchanged

---

## Supported Markdown Styles

### Laravel Style

```
- ## Section Name
    - [Page Title](/docs/url)
```

- Sections: `- ##` prefix
- Pages: indented with 4 spaces, `- [Title](url)` format
- Default `getDepth()` parameters work out of the box

### Samsung Tizen Style

```
# Section Name
## Section Name
## [Page Title](/platform/url)
### [Page Title](/platform/url)
```

- Sections: `#`, `##` headings
- Pages: `## [Title](url)`, `### [Title](url)`
- `getDepth()` requires: `indent: '#'`, `symbol: '#'`, `initialHCount: 1`

### Naver Clova AI Style

```
# Section Name
## Section Name
* [Page Title](/CFR/url)
  * [Page Title](/CFR/url)
```

- Sections: `#`, `##` headings
- Pages: `* [Title](url)` with 2-space indent
- `getDepth()` requires: `indent: '  '`, `symbol: '*'`, `initialHCount: 1`

### Rhymix Style

```
### Section Name
- [Page Title](./ko/url)
- [Page Title](./ko/url)
```

- Sections: `###` heading
- Pages: `- [Title](url)` (no indent)
- `getDepth()` requires: `symbol: '-'`, `initialHCount: 3`

---

## Testing

**Framework**: Pest PHP (v2.x)  
**Test directory**: `tests/`  
**Run command**: `composer test` (alias for `./vendor/bin/pest`)

### Test Structure

```
tests/
├── Feature/
│   ├── TocDirthLaravelTest.php        # Dirty Laravel markdown (with extra text)
│   ├── TocLaravelTest.php             # Clean Laravel markdown
│   ├── TocNaverClovaAiApisDocTest.php # Naver Clova AI markdown
│   └── TocTizenTest.php               # Samsung Tizen markdown
├── Unit/
│   ├── TocTest.php                    # Toc class unit tests
│   ├── ItemTest.php                   # Item class unit tests
│   ├── Convertors/
│   │   ├── CleanConverterTest.php     # CleanConverter tests
│   │   └── CleanJustTopHConverterTest.php  # CleanJustTopHConverter tests
│   ├── Enums/
│   │   └── ItemEnumTest.php           # ItemEnum tests
│   └── Types/
│       └── MarkdownStringTest.php     # MarkdownString tests
└── Fixtures/
    └── docs/                          # Sample markdown files for testing
        ├── dirty_laravel.md
        ├── laravel.md
        ├── naver-clova-ai-apis-doc.md
        ├── rhymix.md
        └── samsung_tizen.md
```

### Key Test Patterns

- **Feature tests**: Use real markdown fixtures, test end-to-end parsing with `Toc::of()`
- **Unit tests**: Test individual methods with inline markdown strings
- **Style-specific tests**: Each document style (Laravel, Tizen, Naver Clova, Rhymix) has its own `describe()` block in `ItemTest.php`
- **Exception testing**: Uses Pest's `->throws()` for expected exceptions
- **Reflection for protected methods**: `normalize()` is tested via `ReflectionMethod` to verify converter pipeline behavior

---

## Common Pitfalls & Gotchas

1. **`$page->title` does not exist**: Always use `$page->getTitle()`. The `Item` class has no public `$title` property.
2. **`getDepth()` requires style-specific parameters**: Default parameters work for Laravel style only. Other styles must pass `indent`, `symbol`, `initialHCount`, and `depth`.
3. **`CleanJustTopHConverter` was previously broken**: It now correctly removes a single top-level heading. The old implementation always returned an empty string.
4. **`normalize()` uses `foreach`**: Not `array_map`. The `array_map` was changed to `foreach` because the closure had side effects.
5. **`getType()` returns an enum, not a string**: Use `$item->getType()->name` to get `"section"` or `"page"` as a string.
6. **`getLine()` throws on invalid index**: It throws `InvalidArgumentException`, not `OutOfBoundsException` or similar.
7. **`toHtml()` always uses `<h2>` for sections**: Regardless of the actual heading level in the markdown. This is a known limitation.
8. **LICENSE file**: The file is named `LICENSE` (not `LICENSE.md`). The README badge link was previously incorrect.

---

## Code Style

- **PSR-12** enforced via Laravel Pint
- `composer lint` — auto-fix issues
- `composer inspect` — check compliance without modifying
