<?php

namespace App\Weapons;

use App\Entity\Game;
use App\GameHelper\Box;

/**
 * Class AbstractWeapon
 */
abstract class AbstractWeapon implements WeaponInterface
{
    protected $numberRotate = 0;

    /**
     * Rotate the matrix
     * @param array<int> $matrix Matrix/Grid to rotate
     *
     * @return array<Box> The matrix rotated
     */
    public function rotate(array $matrix): array
    {
        $time = $this->getNumberRotate();
        if (!$this->isRotate() || $time = 0) {
            return $matrix;
        }

        if ($time > 3) {
            $time %= 4;
        }

        for ($t = $time; $t > 0; $t--) {
            $rows = count($matrix);
            $cols = count($matrix[0]);
            $result = [];

            for ($i = 0; $i < $cols; $i++) {
                for ($j = 0; $j < $rows; $j++) {
                    $result[$rows - $i - 1][$j] = $matrix[$rows - $j - 1][$i];
                }
            }
            $matrix = array_values($result);
        }

        return $matrix;
    }

    /**
     * Get boxes to shoot
     * @param Game $game The game
     * @param int  $x    X position of initial shoot
     * @param int  $y    Y position of initial shoot
     *
     * @return array<Box>
     */
    public function getBoxes(Game $game, int $x, int $y): array
    {
        $gridGame = $game->getGrid();
        $grid = $this->rotate($this->getGrid());
        $rows = count($grid);
        $cols = count($grid[0]);
        $centerY = floor($rows / 2);
        $centerX = floor($cols / 2);

        $boxes = [];
        for ($sy = 0; $sy < $rows; $sy++) {
            for ($sx = 0; $sx < $cols; $sx++) {
                // phpcs:disable SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
                if ($grid[$sy][$sx] === 1) {
                    $boxes[] = Box::createFromGrid($x+$sx-$centerX, $y+$sy-$centerY, $gridGame);
                }
            }
        }

        // Random and shuffle order of boxes
        if ($this->isShuffled()) {
            shuffle($boxes);
        } else {
            $random = mt_rand(0, 1);
            if ($random) {
                $boxes = array_reverse($boxes);
            }
        }
    }

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
