<?php

namespace App\Weapons;

/**
 * Class MortarWeapon
 */
class MortarWeapon extends AbstractWeapon
{
    /**
     * Get the name
     * @return string
     */
    public function getName(): string
    {
        return 'weapon.mortar';
    }

    /**
     * Get the matrix of weapon shoot
     * @return array<int>
     */
    public function getGrid(): array
    {
        return [
            [1, 0, 1],
            [0, 1, 0],
            [1, 0, 1],
        ];
    }

    /**
     * Get the price
     * @return int
     */
    public function getPrice(): int
    {
        return 100;
    }

    /**
     * Can we rotate the weapon ?
     * @return bool
     */
    public function isRotate(): bool
    {
        return false;
    }

    /**
     * AI can use this weapon
     * @return bool
     */
    public function isAiUseIt(): bool
    {
        return true;
    }

    /**
     * Can the shoot is random ?
     * @return bool
     */
    public function isShuffled(): bool
    {
        return true;
    }
}
