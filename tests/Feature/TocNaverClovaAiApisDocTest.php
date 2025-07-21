<?php

use Cable8mm\Toc\Toc;

describe('Simple Toc', function () {
    $markdown = '
# Summary

## Clova Face Recognition
* [CFR API란?](/CFR/API_Guide.md#Overview)
* [사전 준비사항](/CFR/API_Guide.md#Preparation)
* [CFR API 사용하기](/CFR/API_Guide.md#UsingAPI)
* [CFR API 레퍼런스](/CFR/API_Guide.md#APIReference)
    * [유명인 얼굴 인식 API](/CFR/API_Guide.md#CelebrityAPI)
    * [얼굴 감지 API](/CFR/API_Guide.md#FaceAPI)
    * [오류 코드](/CFR/API_Guide.md#ErrorCode)
* [구현 예제](/CFR/API_Guide.md#Examples)
    * [Java](/CFR/API_Guide.md#Java)
    * [PHP](/CFR/API_Guide.md#PHP)
';

    it('should get title', function () use ($markdown) {
        expect(
            Toc::of($markdown)->getLine(0)->getTitle()
        )->toBe('Summary');

        expect(
            Toc::of($markdown)->getLine(1)->getTitle()
        )->toBe('Clova Face Recognition');
    });

    it('should get title with exceptions', function () use ($markdown) {
        expect(
            Toc::of($markdown)->getLine(2)->getTitle()
        );
    })->throws(LogicException::class);
});
