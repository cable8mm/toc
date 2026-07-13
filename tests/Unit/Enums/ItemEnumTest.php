<?php

use Cable8mm\Toc\Enums\ItemEnum;

test('section case exists', function () {
    expect(ItemEnum::section)->toBeInstanceOf(ItemEnum::class);
});

test('page case exists', function () {
    expect(ItemEnum::page)->toBeInstanceOf(ItemEnum::class);
});

test('section and page are distinct', function () {
    expect(ItemEnum::section)->not->toBe(ItemEnum::page);
});
