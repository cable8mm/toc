<?php

namespace Cable8mm\Toc\Converters;

use Cable8mm\Toc\Contracts\ConverterInterface;
use Cable8mm\Toc\Types\MarkdownString;

/**
 * Clean unused lines from markdown
 */
class CleanConverter implements ConverterInterface
{
    /**
     * Clean unused lines from markdown
     */
    public function do(MarkdownString $markdown): MarkdownString
    {
        return new MarkdownString(implode(PHP_EOL,
            array_filter(
                explode(PHP_EOL, $markdown),
                fn ($line) => preg_match('/^\-\s/', $line)
                    || preg_match('/^\s+\-\s/', $line)
                    || preg_match('/^#+/', $line)
            )));
    }
}
