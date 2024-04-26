<?php

namespace Cable8mm\Toc\Types;

use Stringable;

class MarkdownString implements Stringable
{
    /**
     * Create a new Markdown string instance.
     *
     * @param  string  $markdown  The Markdown string.
     * @return void
     */
    public function __construct(
        protected string $markdown = '')
    {

    }

    public function getLink(): ?string
    {
        preg_match('/\[[^\]]+\]\(([^\)+]+)\)/', $this->markdown, $match);

        return $match[1] ?? null;
    }

    public function hasLink(): bool
    {
        return $this->getLink() !== null;
    }

    /**
     * Get the Markdown string.
     */
    public function __toString(): string
    {
        return $this->markdown;
    }
}
