<?php

namespace App\Weapons;

/**
 * Class AbstractWeapon
 */
abstract class AbstractWeapon implements WeaponInterface
{
    /**
     * By default AI don't use the weapon
     * @return bool
     */
    public function isAiUseIt(): bool
    {
        return false;
    }

    /**
     * Convert to string
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }
}
