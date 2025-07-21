<?php

namespace Cable8mm\Toc;

use Cable8mm\Toc\Contracts\ItemInterface;
use Cable8mm\Toc\Enums\ItemEnum;
use Cable8mm\Toc\Types\MarkdownString;
use Stringable;

/**
 * The TOC Item class represents
 */
class Item implements ItemInterface, Stringable
{
    /**
     * Constructor
     *
     * @param  \Cable8mm\Toc\Types\MarkdownString  $markdown  The Markdown string
     */
    protected function __construct(
        protected MarkdownString $markdown
    ) {}

    /**
     * {@inheritDoc}
     */
    public function getTitle(): string
    {
        /**
         * If the markdown contains links,
         */
        if (preg_match('/\[([^\]]+)\]/', $this->markdown, $match) === 1) {
            return trim($match[1]);
        }

        /**
         * Else
         */
        preg_match('/^[#\s\-]+([^\[\]]+)$/', $this->markdown, $match);

        assert($match[1] !== null, new \LogicException('It can not parsed the markdown title from the `'.$this->markdown.'`'));

        return trim($match[1]);
    }

    /**
     * {@inheritDoc}
     */
    public function getLink(): ?string
    {
        /**
         * [...](`link`) be converted to only a link
         */
        preg_match('/\[[^\]]+\]\(([^\)+]+)\)/', $this->markdown, $match);

        return $match[1] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): ItemEnum
    {
        return $this->markdown->hasLink()
            ? ItemEnum::page
            : ItemEnum::section;
    }

    /**
     * {@inheritDoc}
     */
    public function getDepth(?string $indent = '    ', ?string $symbol = '-', ?int $depth = 0, ?int $initialHCount = 0, ?int $initialIndentCount = 0): int
    {
        $hTagCount = strspn(
            $markdown = $this->markdown, '#'
        );

        $baseDepth = ($hTagCount - $initialHCount) < 0 ? 0 : $hTagCount - $initialHCount;
        $depth = $hTagCount > 0 ? 0 : $depth;

        $markdown = ($hTagCount > 0 ? $symbol.' ' : '').preg_replace('/^#\s+/', '', $markdown);

        $indent = preg_quote($indent, '/');
        $symbol = '['.preg_quote($symbol, '/').']';

        if (preg_match('/^'.$symbol.'\s/', $markdown) === 1) {
            return $depth + 1 + $baseDepth;
        }

        if (preg_match('/^'.$indent.$symbol.'\s/', $markdown) === 1) {
            return $depth + 2 + $baseDepth;
        }

        if (
            preg_match('/^\t'.$symbol.'/', $markdown) === 1
            || preg_match('/^'.$indent.$symbol.'/', $markdown) === 1
        ) {
            return $depth + 3 + $baseDepth;
        }

        if (
            preg_match('/^\t\t'.$symbol.'/', $markdown) === 1
            || preg_match('/^'.$indent.$indent.$symbol.'/', $markdown) === 1
        ) {
            return $depth + 4 + $baseDepth;
        }

        throw new \InvalidArgumentException(sprintf('The depth cannot resolved from `%s`', $markdown));
    }

    /**
     * Get the HTML of the given markdown string
     *
     * @return string The method returns the HTML of the given markdown string
     */
    public function toHtml(): string
    {
        $output = '<li>';

        if ($this->getLink() !== null) {
            $output .= '<a href="'.$this->getLink().'">';
        }

        if ($this->getType() === ItemEnum::section) {
            $output .= '<h2>'.$this->getTitle().'</h2>';
        }

        if ($this->getType() === ItemEnum::page) {
            $output .= $this->getTitle();
        }

        if ($this->getLink()) {
            $output .= '</a>';
        }

        $output .= '</li>';

        return $output;
    }

    /**
     * Implement Stringable
     */
    public function __toString(): string
    {
        return $this->toHtml();
    }

    /**
     * Factory
     *
     * @param  string  $markdown  The markdown string
     */
    public static function of(string $markdown): static
    {
        return new static(new MarkdownString($markdown));
    }
}
