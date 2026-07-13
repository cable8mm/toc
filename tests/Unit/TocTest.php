<?php

use Cable8mm\Toc\Converters\CleanConverter;
use Cable8mm\Toc\Toc;

$documents = glob(__DIR__.'/../Fixtures/docs/*.md');

it('should get a file', function () use ($documents) {
    foreach ($documents as $document) {
        expect(
            file_get_contents($document)
        )->toBeString();
    }
});

describe('normalize', function () {
    $method = new ReflectionMethod(Toc::class, 'normalize');
    $method->setAccessible(true);

    test('dirty_laravel', function () use ($method) {
        $markdown = file_get_contents(__DIR__.'/../Fixtures/docs/dirty_laravel.md');

        expect(
            (string) $method->invoke(new Toc($markdown))
        )->not->toContain('Title');
    });

    test('rhymix', function () use ($method) {
        $markdown = file_get_contents(__DIR__.'/../Fixtures/docs/rhymix.md');
        expect(
            (string) $method->invoke(new Toc($markdown))
        )->toContain('코어 개발 참여');
    });
});

test('getLines', function () {
    $markdown = file_get_contents(__DIR__.'/../Fixtures/docs/laravel.md');
    expect(
        Toc::of($markdown)->getLines()
    )->toBeArray();
});

test('getLine returns correct item', function () {
    $markdown = '
    - ## Prologue
        - [Release Notes](/docs/{{version}}/releases)
        - [Upgrade Guide](/docs/{{version}}/upgrade)
    ';

    $toc = Toc::of($markdown);

    expect($toc->getLine(0)->getTitle())->toBe('Prologue');
    expect($toc->getLine(1)->getTitle())->toBe('Release Notes');
    expect($toc->getLine(2)->getTitle())->toBe('Upgrade Guide');
});

test('getLine throws exception for invalid index', function () {
    $markdown = '- ## Prologue';

    Toc::of($markdown)->getLine(99);
})->throws(InvalidArgumentException::class);

test('getSectionTitle returns section title for a given page title', function () {
    $markdown = '
    - ## Prologue
        - [Release Notes](/docs/{{version}}/releases)
        - [Contribution Guide](/docs/{{version}}/contributions)
    - ## Getting Started
        - [Installation](/docs/{{version}}/installation)
        - [Configuration](/docs/{{version}}/configuration)
    ';

    $toc = Toc::of($markdown);

    expect($toc->getSectionTitle('Release Notes'))->toBe('Prologue');
    expect($toc->getSectionTitle('Installation'))->toBe('Getting Started');
    expect($toc->getSectionTitle('Contribution Guide'))->toBe('Prologue');
});

test('getSectionTitle returns null when page title not found', function () {
    $markdown = '
    - ## Prologue
        - [Release Notes](/docs/{{version}}/releases)
    ';

    expect(
        Toc::of($markdown)->getSectionTitle('Non Existent Page')
    )->toBeNull();
});

test('toArray', function () {
    $markdown = '
    - ## Prologue
        - [Release Notes](/docs/{{version}}/releases)
        - [Upgrade Guide](/docs/{{version}}/upgrade)
        - [Contribution Guide](/docs/{{version}}/contributions)
    - ## Getting Started
        - [Installation](/docs/{{version}}/installation)
        - [Configuration](/docs/{{version}}/configuration)
    ';

    $sections = Toc::of($markdown)->toArray();

    expect(count($sections))->toBe(2);

    expect(count($sections[0]['pages']))->toBe(3);

    expect(count($sections[1]['pages']))->toBe(2);

    expect($sections[0]['section']->getTitle())->toBe('Prologue');

    expect($sections[1]['section']->getTitle())->toBe('Getting Started');
});

test('__toString returns normalized markdown', function () {
    $markdown = '
    - ## Prologue
        - [Release Notes](/docs/{{version}}/releases)
    ';

    $toc = Toc::of($markdown);

    expect((string) $toc)->toBeString();
    expect((string) $toc)->toContain('Prologue');
    expect((string) $toc)->toContain('Release Notes');
});

test('addConverters adds additional converters to the pipeline', function () {
    $markdown = '
    - ## Prologue
        - [Release Notes](/docs/{{version}}/releases)
    ';

    $toc = new Toc($markdown);
    // Without normalize, the raw markdown still contains whitespace lines
    // Add CleanConverter again (already in constructor) — just testing it doesn't break
    $toc->addConverters([new CleanConverter]);

    $reflection = new ReflectionMethod(Toc::class, 'normalize');
    $reflection->setAccessible(true);

    $result = (string) $reflection->invoke($toc);

    expect($result)->toContain('Prologue');
    expect($result)->toContain('Release Notes');
});
