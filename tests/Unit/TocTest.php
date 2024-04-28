<?php

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
