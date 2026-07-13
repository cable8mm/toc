<?php

use Cable8mm\Toc\Types\MarkdownString;

test('constructor stores markdown string', function () {
    $markdown = new MarkdownString('# Hello World');

    expect((string) $markdown)->toBe('# Hello World');
});

test('getLink returns link from markdown link syntax', function () {
    $markdown = new MarkdownString('- [Release Notes](/docs/{{version}}/releases)');

    expect($markdown->getLink())->toBe('/docs/{{version}}/releases');
});

test('getLink returns null when no link present', function () {
    $markdown = new MarkdownString('- ## Prologue');

    expect($markdown->getLink())->toBeNull();
});

test('hasLink returns true when link exists', function () {
    $markdown = new MarkdownString('- [Release Notes](/docs/{{version}}/releases)');

    expect($markdown->hasLink())->toBeTrue();
});

test('hasLink returns false when no link exists', function () {
    $markdown = new MarkdownString('- ## Prologue');

    expect($markdown->hasLink())->toBeFalse();
});

test('can be constructed with empty string', function () {
    $markdown = new MarkdownString;

    expect((string) $markdown)->toBe('');
    expect($markdown->getLink())->toBeNull();
    expect($markdown->hasLink())->toBeFalse();
});
