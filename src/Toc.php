<?php

namespace Cable8mm\Toc;

use Cable8mm\Toc\Converters\CleanConverter;
use Cable8mm\Toc\Enums\ItemEnum;
use Cable8mm\Toc\Types\MarkdownString;
use Stringable;

class Toc implements Stringable
{
    /**
     * The converter array
     *
     * @var \Cable8mm\Toc\Contracts\ConverterInterface[]
     */
    protected array $converters = [];

    /**
     * The markdown string for the converter
     */
    protected MarkdownString $data;

    /**
     * The line array
     *
     * @var \Cable8mm\Toc\Item[]
     */
    protected array $lines = [];

    /**
     * The section array
     *
     * @example $sections[0]['section']->getTitle()
     * @example $sections[0]['pages'][0]->getTitle()
     */
    protected array $sections = [];

    /**
     * Constructor
     *
     * @param  string  $markdown  The original markdown string
     */
    public function __construct(
        protected string $markdown)
    {
        $this->converters = [
            new CleanConverter,
        ];

        $this->data = new MarkdownString($markdown);
    }

    /**
     * Converts markdown string using the converters provided
     */
    protected function normalize(): static
    {
        array_map(
            function ($converter) {
                /** @var \Cable8mm\Toc\Contracts\ConverterInterface $converter */
                $this->data = $converter->do($this->data);
            },
            $this->converters
        );

        return $this;
    }

    /**
     * Map lines to Item instance
     */
    protected function mapping(): static
    {
        $this->lines = array_map(
            function ($line) {
                return Item::of($line);
            },
            explode(PHP_EOL, $this->data)
        );

        return $this;
    }

    protected function grouping(): static
    {
        $sectionKey = -1;

        foreach ($this->lines as $key => $line) {
            if ($line->getType() === ItemEnum::section) {
                $sectionKey++;
                $this->sections[$sectionKey]['section'] = $line;
            }

            if ($line->getType() === ItemEnum::page) {
                $this->sections[$sectionKey]['pages'][] = $line;
            }
        }

        return $this;
    }

    /**
     * Get the section title
     *
     * @param  string  $title  The title
     * @return string|null The method returns the section title if found, null otherwise
     */
    public function getSectionTitle(string $title): ?string
    {
        foreach ($this->sections as $section) {
            foreach ($section['pages'] as $page) {
                if ($title === $page->title) {
                    return $section['section'];
                }
            }
        }

        return null;
    }

    /**
     * Get the toc line array from a markdown string
     *
     * @return \Cable8mm\Toc\Item[] The method returns line array from a markdown string
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * Get the toc nth line from a markdown string
     *
     * @param  int  $lineNumber  The line number
     * @return \Cable8mm\Toc\Item The method returns nth line item from a markdown string
     */
    public function getLine(int $lineNumber): \Cable8mm\Toc\Item
    {
        return $this->lines[$lineNumber] ?? throw new \InvalidArgumentException('No such line number was found for line '.$lineNumber);
    }

    /**
     * Add converters to the converter array
     *
     * @param  \Cable8mm\Toc\Contracts\ConverterInterface[]  $converters  The converter array
     */
    public function addConverters(array $converters): static
    {
        $this->converters = [...$this->converters, ...$converters];

        return $this;
    }

    /**
     * Implement `Stringable` Interface
     *
     * @return string The method returns a string representation
     */
    public function __toString(): string
    {
        return (string) $this->data;
    }

    /**
     * Output the navigation data
     *
     * @return array The method returns the navigation data
     */
    public function toArray(): array
    {
        return $this->sections;
    }

    /**
     * Factory
     *
     * @param  string  $markdown  The markdown string
     */
    public static function of(string $markdown): static
    {
        return (new static($markdown))->normalize()->mapping()->grouping();
    }
}
