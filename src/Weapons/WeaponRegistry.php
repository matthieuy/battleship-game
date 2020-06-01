<?php

namespace App\Weapons;

/**
 * Class WeaponRegistry
 */
class WeaponRegistry
{
    protected $weapons = [];

    /**
     * WeaponRegistry constructor.
     * @param iterable<AbstractWeapon> $weapons
     */
    public function __construct(iterable $weapons)
    {
        foreach ($weapons as $weapon) {
            foreach ($weapon->getIterator() as $item) {
                $this->weapons[$item->getName()] = $item;
            }
        }
    }

    /**
     * Get all weapons
     * @return array<WeaponInterface>
     */
    public function getAllWeapons(): array
    {
        return $this->weapons;
    }
}
