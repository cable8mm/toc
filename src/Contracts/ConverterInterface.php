<?php

namespace Cable8mm\Toc\Contracts;

use Cable8mm\Toc\Types\MarkdownString;

interface ConverterInterface
{
    public function do(MarkdownString $toc): MarkdownString;
}
