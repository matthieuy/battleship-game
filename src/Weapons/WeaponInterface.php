<?php

namespace App\Weapons;

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Interface WeaponInterface
 */
interface WeaponInterface
{
    /**
     * Get the name
     * @Groups("weapon")
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the price
     * @Groups("weapon")
     *
     * @return int
     */
    public function getPrice(): int;

    /**
     * Can we rotate the weapon ?
     * @Groups("weapon")
     *
     * @return bool
     */
    public function isRotate(): bool;

    /**
     * Get the matrix of weapon shot
     * @Groups("weapon")
     *
     * @return array<int>
     */
    public function getGrid(): array;

    /**
     * Can a AI use this weapon
     * @return bool
     */
    public function isAiUseIt(): bool;

    /**
     * Can the shoot is random
     * @return bool
     */
    public function isShuffled(): bool;

    /**
     * Convert to string
     * @return string
     */
    public function __toString(): string;
}
