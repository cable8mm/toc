<?php

namespace Cable8mm\Toc\Contracts;

use Cable8mm\Toc\Enums\ItemEnum;

interface ItemInterface
{
    /**
     * Get the title of the navigation item
     *
     * @return string The method returns the title of the navigation item
     */
    public function getTitle(): string;

    /**
     * Get the link of the navigation item
     *
     * @return string The method returns the link of the navigation item
     */
    public function getLink(): ?string;

    /**
     * Get the type of the navigation item
     *
     * @return \Cable8mm\Toc\Enums\ItemEnum The method returns the type of the navigation item
     */
    public function getType(): ItemEnum;

    /**
     * Get the depth of the navigation item
     *
     * @param  string|null  $indent  The indent e.g. "-" or "*"
     * @param  string|null  $symbol  The symbol e.g. "-" or "*"
     * @param  int|null  $depth  The depth e.g. 1, 2, 3, 4
     * @param  int|null  $initialHCount  The initial # count
     * @param  int|null  $initialIndentCount  The initial indent count
     * @return int The method returns the depth of the navigation item
     *
     * @throws \InvalidArgumentException
     */
    public function getDepth(?string $indent = '    ', ?string $symbol = '-', ?int $depth = 0, ?int $initialHCount = 0, ?int $initialIndentCount = 0): int;
}
