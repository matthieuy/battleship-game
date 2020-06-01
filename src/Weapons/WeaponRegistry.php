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

    /**
     * Get a specific name
     * @param string $name
     *
     * @return WeaponInterface|null
     */
    public function getWeapon(string $name): ?WeaponInterface
    {
        if (array_key_exists($name, $this->weapons)) {
            return $this->weapons[$name];
        }

        return null;
    }
}
