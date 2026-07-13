<?php

use Cable8mm\Toc\Toc;

describe('Samsung Tizen Toc', function () {
    $markdown = '
# What is Tizen?

## Versions

## [Overview](/platform/what-is-tizen/overview.md)

### [TV](/platform/what-is-tizen/profiles/tv.md)

### [Mobile](/platform/what-is-tizen/profiles/mobile.md)

## [Application](/platform/what-is-tizen/application.md)
';

    it('should get title', function () use ($markdown) {
        expect(
            Toc::of($markdown)->getLine(0)->getTitle()
        )->toBe('What is Tizen?');

        expect(
            Toc::of($markdown)->getLine(1)->getTitle()
        )->toBe('Versions');

        expect(
            Toc::of($markdown)->getLine(2)->getTitle()
        )->toBe('Overview');

        expect(
            Toc::of($markdown)->getLine(3)->getTitle()
        )->toBe('TV');
    });
});
