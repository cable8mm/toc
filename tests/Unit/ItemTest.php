<?php

use Cable8mm\Toc\Enums\ItemEnum;
use Cable8mm\Toc\Item;

describe('Laravel Style', function () {
    test('getTitle', function () {
        expect(Item::of('- ## Prologue')->getTitle())->toBe('Prologue');
        expect(Item::of('    - [Release Notes](/docs/{{version}}/releases)')->getTitle())->toBe('Release Notes');
        expect(Item::of('- [API Documentation](/api/11.x)')->getTitle())->toBe('API Documentation');
    });

    test('getLink', function () {
        expect(Item::of('- ## Prologue')->getLink())->toBeNull();
        expect(Item::of('    - [Release Notes](/docs/{{version}}/releases)')->getLink())->toBe('/docs/{{version}}/releases');
        expect(Item::of('- [API Documentation](/api/11.x)')->getLink())->toBe('/api/11.x');
    });

    test('getType', function () {
        expect(Item::of('- ## Prologue')->getType())->toBe(ItemEnum::section);
        expect(Item::of('    - [Release Notes](/docs/{{version}}/releases)')->getType())->toBe(ItemEnum::page);
        expect(Item::of('- [API Documentation](/api/11.x)')->getType())->toBe(ItemEnum::page);
    });

    test('getDepth', function () {
        expect(Item::of('- ## Prologue')->getDepth())->toBe(1);
        expect(Item::of('    - [Release Notes](/docs/{{version}}/releases)')->getDepth())->toBe(2);
        expect(Item::of('- [API Documentation](/api/11.x)')->getDepth())->toBe(1);
    });
});

describe('Tizen Style', function () {
    test('getTitle', function () {
        expect(Item::of('# What is Tizen?')->getTitle())->toBe('What is Tizen?');
        expect(Item::of('## Versions')->getTitle())->toBe('Versions');
        expect(Item::of('## [Overview](/platform/what-is-tizen/overview.md)')->getTitle())->toBe('Overview');
        expect(Item::of('### [TV](/platform/what-is-tizen/profiles/tv.md)')->getTitle())->toBe('TV');
    });

    test('getLink', function () {
        expect(Item::of('# What is Tizen?')->getLink())->toBeNull();
        expect(Item::of('## Versions')->getLink())->toBeNull();
        expect(Item::of('## [Overview](/platform/what-is-tizen/overview.md)')->getLink())->toBe('/platform/what-is-tizen/overview.md');
        expect(Item::of('### [TV](/platform/what-is-tizen/profiles/tv.md)')->getLink())->toBe('/platform/what-is-tizen/profiles/tv.md');
    });

    test('getType', function () {
        expect(Item::of('# What is Tizen?')->getType())->toBe(ItemEnum::section);
        expect(Item::of('## Versions')->getType())->toBe(ItemEnum::section);
        expect(Item::of('## [Overview](/platform/what-is-tizen/overview.md)')->getType())->toBe(ItemEnum::page);
        expect(Item::of('### [TV](/platform/what-is-tizen/profiles/tv.md)')->getType())->toBe(ItemEnum::page);
    });

    test('getDepth', function () {
        expect(Item::of('# What is Tizen?')->getDepth(indent: '#', symbol: '#', initialHCount: 1, depth: 0))->toBe(1);
        expect(Item::of('## Versions')->getDepth(indent: '#', symbol: '#', initialHCount: 1, depth: 1))->toBe(2);
        expect(Item::of('## [Overview](/platform/what-is-tizen/overview.md)')->getDepth(indent: '#', symbol: '#', initialHCount: 1, depth: 2))->toBe(2);
        expect(Item::of('### [TV](/platform/what-is-tizen/profiles/tv.md)')->getDepth(indent: '#', symbol: '#', initialHCount: 1, depth: 2))->toBe(3);
    });
});

describe('Naver Clova Style', function () {
    test('getTitle', function () {
        expect(Item::of('# Summary')->getTitle())->toBe('Summary');
        expect(Item::of('## Clova Face Recognition')->getTitle())->toBe('Clova Face Recognition');
        expect(Item::of('* [CFR API란?](/CFR/API_Guide.md#Overview)')->getTitle())->toBe('CFR API란?');
        expect(Item::of('  * [유명인 얼굴 인식 API](/CFR/API_Guide.md#CelebrityAPI)')->getTitle())->toBe('유명인 얼굴 인식 API');
    });

    test('getLink', function () {
        expect(Item::of('# Summary')->getLink())->toBeNull();
        expect(Item::of('## Clova Face Recognition')->getLink())->toBeNull();
        expect(Item::of('* [CFR API란?](/CFR/API_Guide.md#Overview)')->getLink())->toBe('/CFR/API_Guide.md#Overview');
        expect(Item::of('  * [유명인 얼굴 인식 API](/CFR/API_Guide.md#CelebrityAPI)')->getLink())->toBe('/CFR/API_Guide.md#CelebrityAPI');
    });

    test('getType', function () {
        expect(Item::of('# Summary')->getType())->toBe(ItemEnum::section);
        expect(Item::of('## Clova Face Recognition')->getType())->toBe(ItemEnum::section);
        expect(Item::of('* [CFR API란?](/CFR/API_Guide.md#Overview)')->getType())->toBe(ItemEnum::page);
        expect(Item::of('  * [유명인 얼굴 인식 API](/CFR/API_Guide.md#CelebrityAPI)')->getType())->toBe(ItemEnum::page);
    });

    test('getDepth', function () {
        expect(Item::of('# Summary')->getDepth(indent: '  ', symbol: '*', initialHCount: 1, depth: 0))->toBe(1);
        expect(Item::of('## Clova Face Recognition')->getDepth(indent: '  ', symbol: '*', initialHCount: 1, depth: 1))->toBe(2);
        expect(Item::of('* [CFR API란?](/CFR/API_Guide.md#Overview)')->getDepth(indent: '  ', symbol: '*', initialHCount: 1, depth: 2))->toBe(3);
        expect(Item::of('  * [유명인 얼굴 인식 API](/CFR/API_Guide.md#CelebrityAPI)')->getDepth(indent: '  ', symbol: '*', initialHCount: 1, depth: 2))->toBe(4);
    });
});

describe('Rhymix Clova Style', function () {
    test('getTitle', function () {
        expect(Item::of('### 개요')->getTitle())->toBe('개요');
        expect(Item::of('- [설치 환경](./ko/introduction/requirements.md)')->getTitle())->toBe('설치 환경');
        expect(Item::of('- [라이믹스 설치](./ko/introduction/install.md)')->getTitle())->toBe('라이믹스 설치');
    });

    test('getLink', function () {
        expect(Item::of('### 개요')->getLink())->toBeNull();
        expect(Item::of('- [설치 환경](./ko/introduction/requirements.md)')->getLink())->toBe('./ko/introduction/requirements.md');
        expect(Item::of('- [라이믹스 설치](./ko/introduction/install.md)')->getLink())->toBe('./ko/introduction/install.md');
    });

    test('getType', function () {
        expect(Item::of('### 개요')->getType())->toBe(ItemEnum::section);
        expect(Item::of('- [설치 환경](./ko/introduction/requirements.md)')->getType())->toBe(ItemEnum::page);
        expect(Item::of('- [라이믹스 설치](./ko/introduction/install.md)')->getType())->toBe(ItemEnum::page);
    });

    test('getDepth', function () {
        expect(Item::of('### 개요')->getDepth(symbol: '*', initialHCount: 3, depth: 0))->toBe(1);
        expect(Item::of('- [설치 환경](./ko/introduction/requirements.md)')->getDepth(symbol: '-', initialHCount: 3, depth: 1))->toBe(2);
        expect(Item::of('- [라이믹스 설치](./ko/introduction/install.md)')->getDepth(symbol: '-', initialHCount: 3, depth: 1))->toBe(2);
    });
});
