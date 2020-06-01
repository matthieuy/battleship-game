<?php

namespace App\Weapons;

/**
 * Class AbstractWeapon
 */
abstract class AbstractWeapon implements WeaponInterface
{
    protected $numberRotate = 0;

    /**
     * Set NumberRotate
     * @param int $numberRotate
     *
     * @return $this
     */
    public function setNumberRotate(int $numberRotate): self
    {
        if ($numberRotate > 3) {
            $this->numberRotate = $numberRotate % 4;
        }
        $this->numberRotate = $numberRotate;

        return $this;
    }

    /**
     * Get number of rotate
     * @return int
     */
    public function getNumberRotate(): int
    {
        return $this->numberRotate;
    }

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
