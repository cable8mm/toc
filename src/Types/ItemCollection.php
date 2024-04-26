<?php

namespace Cable8mm\Toc\Types;

use Cable8mm\Toc\Contracts\ItemCollectionInterface;

/**
 * The TOC Item collection class represents
 */
class ItemCollection implements ItemCollectionInterface
{
    public function __construct(
        protected MarkdownString $markdown
    ) {

    }

    public function normalize(): string
    {
        return $this->markdown;
    }

    /**
     * {@inheritDoc}
     */
    public static function of(string $markdown): static
    {
        return new static(new MarkdownString($markdown));
    }
}
