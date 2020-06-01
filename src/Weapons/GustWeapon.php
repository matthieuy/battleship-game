<?php

namespace App\Weapons;

/**
 * Class GustWeapon
 */
class GustWeapon extends AbstractWeapon
{
    /**
     * Get the name
     * @return string
     */
    public function getName(): string
    {
        return 'weapon.gust';
    }

    /**
     * Get the matrix of weapon shoot
     * @return array<int>
     */
    public function getGrid(): array
    {
        return [[1], [1], [1], [1], [1]];
    }

    /**
     * Get the price
     * @return int
     */
    public function getPrice(): int
    {
        return 80;
    }

    /**
     * Can we rotate the weapon ?
     * @return bool
     */
    public function isRotate(): bool
    {
        return true;
    }

    /**
     * Can the shoot is random ?
     * @return bool
     */
    public function isShuffled(): bool
    {
        return false;
    }
}
