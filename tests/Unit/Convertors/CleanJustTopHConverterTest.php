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

    expect(
        (new CleanJustTopHConverter())->do(new MarkdownString($markdown))
    )->not->toBe('Unused Section title');
});

// test('laravel style 2', function () {
//     $markdown = '
//     - ## Prologue
//         - [Contribution Guide](/docs/{{version}}/contributions)
//     - ## Getting Started
//         - [Configuration](/docs/{{version}}/configuration)
//     ';

//     $lines = (new CleanJustTopHConverter())->do(new MarkdownString($markdown));

//     expect(
//         preg_match_all('/^([^\r\n\w]+|)$/m', $lines)
//     )->toBe(0);
// });
