<?php

namespace App\Weapons;

/**
 * Class FlashWeapon
 */
class FlashWeapon extends AbstractWeapon
{
    /**
     * Get the name
     * @return string
     */
    public function getName(): string
    {
        return 'weapon.flash';
    }

    /**
     * Get the matrix of weapon shoot
     * @return array<int>
     */
    public function getGrid(): array
    {
        return [
            [0, 1],
            [1, 0],
            [0, 1],
            [1, 0],
            [0, 1],
        ];
    }

    /**
     * Get the price
     * @return int
     */
    public function getPrice(): int
    {
        return 120;
    }

    /**
     * Can we rotate the weapon
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
