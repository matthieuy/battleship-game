<?php

namespace App\Weapons;

use App\Entity\Game;
use App\GameHelper\Box;
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
     * Set rotate
     * @param int $rotate
     *
     * @return $this
     */
    public function setNumberRotate(int $rotate): self;

    /**
     * Get number of rotate
     * @return int
     */
    public function getNumberRotate(): int;

    /**
     * Get boxes to shoot
     * @param Game $game The game
     * @param int  $x    X position of initial shoot
     * @param int  $y    Y position of initial shoot
     *
     * @return array<Box>
     */
    public function getBoxes(Game $game, int $x, int $y): array;

    /**
     * Rotate the matrix
     * @param array<int> $matrix Matrix/Grid to rotate
     *
     * @return array<Box> The matrix rotated
     */
    public function rotateMatrix(array $matrix): array;

    /**
     * Convert to string
     * @return string
     */
    public function __toString(): string;
}
