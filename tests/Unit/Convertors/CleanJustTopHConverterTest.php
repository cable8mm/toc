<?php

use Cable8mm\Toc\Converters\CleanJustTopHConverter;
use Cable8mm\Toc\Types\MarkdownString;

test('remove unused section title', function () {
    $markdown = '# Unused Section title
- ## Prologue
    - [Release Notes](/docs/{{version}}/releases)
    - [Upgrade Guide](/docs/{{version}}/upgrade)
    - [Contribution Guide](/docs/{{version}}/contributions)
- ## Getting Started
    - [Installation](/docs/{{version}}/installation)
    - [Configuration](/docs/{{version}}/configuration)';

    $result = (new CleanJustTopHConverter)->do(new MarkdownString($markdown));

    expect((string) $result)->not->toContain('Unused Section title');
    expect((string) $result)->toContain('Prologue');
    expect((string) $result)->toContain('Getting Started');
});

test('does not remove section titles when multiple top level headings exist', function () {
    $markdown = '# Chapter 1
- ## Section 1
    - [Page 1](/page1)
- ## Section 2
    - [Page 2](/page2)
# Chapter 2
- ## Section 3
    - [Page 3](/page3)';

    $result = (new CleanJustTopHConverter)->do(new MarkdownString($markdown));

    expect((string) $result)->toContain('Chapter 1');
    expect((string) $result)->toContain('Chapter 2');
});

test('does not modify markdown without headings', function () {
    $markdown = '- [Item 1](/item1)
    - [Item 2](/item2)';

    $result = (new CleanJustTopHConverter)->do(new MarkdownString($markdown));

    expect((string) $result)->toBe($markdown);
});

test('does not modify markdown when no single top heading exists', function () {
    $markdown = '
    - ## Prologue
        - [Contribution Guide](/docs/{{version}}/contributions)
    - ## Getting Started
        - [Configuration](/docs/{{version}}/configuration)';

    $result = (new CleanJustTopHConverter)->do(new MarkdownString($markdown));

    expect((string) $result)->toContain('Prologue');
    expect((string) $result)->toContain('Getting Started');
});
