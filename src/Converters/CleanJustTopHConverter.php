<?php

namespace Cable8mm\Toc\Converters;

use Cable8mm\Toc\Contracts\ConverterInterface;
use Cable8mm\Toc\Types\MarkdownString;

/**
 * Clean top H tags if it has been alone
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

        $output = '';

        foreach ($lines as $line) {
            $hCount = strspn($line, '#');

            if ($hCount < $topHCount) {
                $topHRowCount++;
                $topHCount = $hCount;
            }

            if ($hCount === $topHCount) {
                $topHRowCount++;
            }
        }

        if ($topHCount !== 6 && $topHRowCount === 1) {

        }

        return new MarkdownString($output);
    }
}
