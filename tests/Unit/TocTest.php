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

it('should clean unused lines', function () {
    $markdown = file_get_contents(__DIR__.'/../Fixtures/docs/dirty_laravel.md');
    expect(
        (string) (new Toc($markdown))->normalize()
    )->not->toContain('Title');
});

it('should get a title with sharp', function () {
    $markdown = file_get_contents(__DIR__.'/../Fixtures/docs/rhymix.md');
    expect(
        (string) (new Toc($markdown))->normalize()
    )->toContain('코어 개발 참여');
});
