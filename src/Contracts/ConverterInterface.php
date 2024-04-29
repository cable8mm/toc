<?php

namespace Cable8mm\Toc\Contracts;

use Cable8mm\Toc\Types\MarkdownString;

interface ConverterInterface
{
    /**
     * Convertor method to convert a string representation
     */
    public function do(MarkdownString $toc): MarkdownString;
}
