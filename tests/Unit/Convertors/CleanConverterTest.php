<?php

use Cable8mm\Toc\Converters\CleanConverter;
use Cable8mm\Toc\Types\MarkdownString;

describe('do', function () {
    test('laravel style 1', function () {
        $markdown = '
        - ## Prologue
    
            - [Release Notes](/docs/{{version}}/releases)
            - [Upgrade Guide](/docs/{{version}}/upgrade)
            - [Contribution Guide](/docs/{{version}}/contributions)
        - ## Getting Started
            - [Installation](/docs/{{version}}/installation)
            - [Configuration](/docs/{{version}}/configuration)
        ';

        $lines = (new CleanConverter)->do(new MarkdownString($markdown));

        expect(
            preg_match_all('/^([^\r\n\w]+|)$/m', $lines)
        )->toBe(0);
    });

    test('laravel style 2', function () {
        $markdown = '
        - ## Prologue
            - [Contribution Guide](/docs/{{version}}/contributions)
        - ## Getting Started
            - [Configuration](/docs/{{version}}/configuration)
        ';

        $lines = (new CleanConverter)->do(new MarkdownString($markdown));

        expect(
            preg_match_all('/^([^\r\n\w]+|)$/m', $lines)
        )->toBe(0);
    });

});
