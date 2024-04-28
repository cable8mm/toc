<?php

namespace Cable8mm\Toc;

use Cable8mm\Toc\Converters\CleanConverter;
use Cable8mm\Toc\Types\MarkdownString;
use Stringable;

class Toc implements Stringable
{
    /**
     * @var \Cable8mm\Toc\Contracts\ConverterInterface[] The converter array
     */
    private array $converters = [];

    protected MarkdownString $data;

    /**
     * @var \Cable8mm\Toc\Item[] The item array
     */
    protected array $lines = [];

    /**
     * Constructor
     *
     * @param  string  $markdown  The original markdown string
     * @param  array  $converters  The parsers array
     */
    public function __construct(
        protected string $markdown)
    {
        $this->converters = [
            new CleanConverter,
        ];

        $this->data = new MarkdownString($markdown);
    }

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

    public function getLines(): array
    {
        return $this->lines;
    }

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

    public function __toString(): string
    {
        return (string) $this->data;
    }

    public static function of(string $markdown): static
    {
        return (new static($markdown))->normalize()->mapping();
    }
}
