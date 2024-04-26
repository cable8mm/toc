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
     * @return string The method returns the type of the navigation item
     */
    public function getType(): ItemEnum;

    /**
     * Get the depth of the navigation item
     *
     * @return string The method returns the depth of the navigation item
     *
     * @throws \InvalidArgumentException
     */
    public function getDepth(): int;
}
