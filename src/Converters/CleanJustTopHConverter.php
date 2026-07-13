<?php

namespace Cable8mm\Toc\Converters;

use Cable8mm\Toc\Contracts\ConverterInterface;
use Cable8mm\Toc\Types\MarkdownString;

/**
 * Clean top H tags if it has been alone
 *
 * Removes a top-level heading (with the fewest # characters)
 * if it appears alone and no other line uses the same heading level.
 * This handles cases like a document title that doesn't belong in the TOC.
 */
class CleanJustTopHConverter implements ConverterInterface
{
    /**
     * Clean unused lines from markdown
     *
     * rule 1: If the first line is a section and next line is also a section
     * rule 2: The lines is the only line with the fewest number of H tags
     *
     * If rule 1 && rule 2 then the lines must be removed
     */
    public function do(MarkdownString $markdown): MarkdownString
    {
        $lines = explode(PHP_EOL, $markdown);
        $topHCount = 6;
        $topHRowCount = 0;

        foreach ($lines as $line) {
            $hCount = strspn($line, '#');

            if ($hCount > 0 && $hCount < $topHCount) {
                $topHRowCount = 1;
                $topHCount = $hCount;
            } elseif ($hCount === $topHCount) {
                $topHRowCount++;
            }
        }

        // If there's a heading level used exactly once and it's the topmost level, remove that line
        if ($topHCount !== 6 && $topHRowCount === 1) {
            $lines = array_filter($lines, function ($line) use ($topHCount) {
                return strspn($line, '#') !== $topHCount;
            });
        }

        return new MarkdownString(implode(PHP_EOL, array_values($lines)));
    }
}
