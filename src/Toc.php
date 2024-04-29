<?php

namespace Cable8mm\Toc;

use Cable8mm\Toc\Converters\CleanConverter;
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
     * The item array
     *
     * @var \Cable8mm\Toc\Item[]
     */
    protected array $lines = [];

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
     * Factory
     *
     * @param  string  $markdown  The markdown string
     */
    public static function of(string $markdown): static
    {
        return (new static($markdown))->normalize()->mapping();
    }
}
