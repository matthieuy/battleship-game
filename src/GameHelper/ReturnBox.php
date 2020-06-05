<?php

namespace App\GameHelper;

use App\Boats;
use App\Weapons\WeaponInterface;

/**
 * Class ReturnBox
 */
class ReturnBox
{
    /** @var Box[] */
    protected $listBox;
    protected $weapon = null;

    /**
     * ReturnBox constructor.
     */
    public function __construct()
    {
        $this->listBox = [];
    }

    /**
     * Get Weapon
     * @return WeaponInterface|null
     */
    public function getWeapon(): ?WeaponInterface
    {
        return $this->weapon;
    }

    /**
     * Set Weapon
     * @param WeaponInterface|null $weapon
     *
     * @return $this
     */
    public function setWeapon(?WeaponInterface $weapon = null): self
    {
        $this->weapon = $weapon;

        return $this;
    }

    /**
     * Add box to return
     * @param Box $box
     *
     * @return $this
     */
    public function addBox(Box $box): self
    {
        $this->listBox[] = $box;

        return $this;
    }

    /**
     * Update and add sink infos
     * @param array<mixed> $grid
     * @param int          $boatLength
     *
     * @return $this
     */
    public function updateSink(array $grid, int $boatLength): self
    {
        $boxIndex = count($this->listBox) - 1;
        $box = $this->listBox[$boxIndex];

        foreach ($grid as $y => $row) {
            foreach ($row as $x => $b) {
                // phpcs:disable SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
                if (array_key_exists('boat', $b) && $b['boat'] === $box->getBoat()) {
                    $box
                        ->addSinkInfo([
                            'x' => $x,
                            'y' => $y,
                            'img' => Boats::getDeadImg($b['img'], $boatLength),
                            'shoot' => $b['shoot'],
                            'player' => $b['player'],
                            'dead' => true,
                        ])
                        ->setDead(true);
                }
            }
        }

        return $this;
    }
}
