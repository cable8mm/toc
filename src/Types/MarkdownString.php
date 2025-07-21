<?php

namespace Cable8mm\Toc\Types;

use Stringable;

class MarkdownString implements Stringable
{
    /**
     * Constructor
     *
     * @param  string  $markdown  The Markdown string.
     * @return void
     *
     * @example new MarkdownString('# foo')
     */
    public function __construct(
        protected string $markdown = '') {}

    /**
     * Get the link from the Markdown string
     *
     * @return string|null The method returns the link from the Markdown string or null if not found in the Markdown string
     */
    public function getLink(): ?string
    {
        preg_match('/\[[^\]]+\]\(([^\)+]+)\)/', $this->markdown, $match);

        return $match[1] ?? null;
    }

    /**
     * Has the link or not?
     *
     * @return bool The method returns true if the link
     */
    public function hasLink(): bool
    {
        return $this->getLink() !== null;
    }

    /**
     * Implements `Stringable` interface
     */
    public function __toString(): string
    {
        return $this->markdown;
    }
}
