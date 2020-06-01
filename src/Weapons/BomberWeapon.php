<?php

namespace App\Weapons;

/**
 * Class BomberWeapon
 */
class BomberWeapon extends AbstractWeapon
{
    /**
     * Get the name
     * @return string
     */
    public function getName(): string
    {
        return 'weapon.bomber';
    }

    /**
     * Get the matrix of weapon shoot
     * @return array<int>
     */
    public function getGrid(): array
    {
        return [
            [0, 1, 0],
            [1, 0, 1],
            [0, 1, 0],
        ];
    }

    /**
     * Get the price
     * @return int
     */
    public function getPrice(): int
    {
        return 60;
    }

    /**
     * Can we rotate the weapon
     * @return bool
     */
    public function isRotate(): bool
    {
        return false;
    }

    /**
     * Can a AI use this weapon
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
