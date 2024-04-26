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

    public function normalize(): static
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
        return $this->data;
    }

    public static function of(string $markdown): static
    {
        return new static($markdown);
    }
}
