<?php

use Cable8mm\Toc\Toc;

describe('Simple Toc', function () {
    $markdown = '
- ## Prologue
    - [Release Notes](/docs/{{version}}/releases)
    - [Upgrade Guide](/docs/{{version}}/upgrade)
    - [Contribution Guide](/docs/{{version}}/contributions)
- ## Getting Started
    - [Installation](/docs/{{version}}/installation)
    - [Configuration](/docs/{{version}}/configuration)
';

    it('should get title', function () use ($markdown) {
        expect(
            Toc::of($markdown)->getLine(0)->getTitle()
        )->toBe('Prologue');

        expect(
            Toc::of($markdown)->getLine(1)->getTitle()
        )->toBe('Release Notes');

        expect(
            Toc::of($markdown)->getLine(2)->getTitle()
        )->toBe('Upgrade Guide');
    });
});
