<?php

namespace App\GameHelper;

use App\Entity\Player;

/**
 * Class Box
 */
class Box
{
    private $x;
    private $y;
    private $img;
    private $shoot;
    private $player;
    private $team;
    private $boat;

    /**
     * Box constructor.
     * @param int $x
     * @param int $y
     */
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Create a box and populate it
     * @param int          $x
     * @param int          $y
     * @param array<mixed> $gridOrBox
     *
     * @return Box
     */
    public static function createFromBox(int $x, int $y, array $gridOrBox): Box
    {
        $box = new Box($x, $y);
        $box->populateFromGrid($gridOrBox);

        return $box;
    }

    /**
     * Get X
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * Get Y
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * Get Img
     * @return int|null
     */
    public function getImg(): ?int
    {
        return $this->img;
    }

    /**
     * Set Img
     * @param int $img
     *
     * @return $this
     */
    public function setImg(int $img): self
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Is box is empty
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->img === null;
    }

    /**
     * Set the shooter
     * @param Player|null $shooter
     *
     * @return $this
     */
    public function setShooter(?Player $shooter = null): self
    {
        $this->shoot = $shooter === null ? 0 : $shooter->getPosition();
    }

    /**
     * Already shoot
     * @return bool
     */
    public function isAlreadyShoot(): bool
    {
        return $this->shoot !== null;
    }

    /**
     * Get player position
     * @return int|null
     */
    public function getPlayer(): ?int
    {
        return $this->player;
    }

    /**
     * Get team number
     * @return int|null
     */
    public function getTeam(): ?int
    {
        return $this->team;
    }

    /**
     * Get boat number
     * @return int|null
     */
    public function getBoat(): ?int
    {
        return $this->boat;
    }

    /**
     * Populate box
     * @param array<mixed> $gridOrBox The complete grid or the box
     *
     * @return $this
     */
    public function populateFromGrid(array $gridOrBox): self
    {
        $firstItem = array_key_first($gridOrBox);
        $box = [];
        if (is_string($firstItem)) {
            // $gridOrBox is the box
            $box = $gridOrBox;
        } else {
            // $gridOrBox is the complet grid
            if (isset($gridOrBox[$this->y][$this->x])) {
                $box = $gridOrBox[$this->y][$this->x];
            }
        }

        // Populate
        foreach ($box as $k => $v) {
            // phpcs:disable SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }

        return $this;
    }

    /**
     * Get infos to return
     * @param bool $toSave Get infos for save
     *
     * @return array<mixed>
     */
    public function getInfosToReturn(bool $toSave = false): array
    {
        $infos = [
            'x' => $this->x,
            'y' => $this->y,
            'img' => $this->img,
        ];
        if ($this->shoot !== null) {
            $infos['shoot'] = $this->shoot;
        }
        if ($this->player !== null) {
            $infos = array_merge($infos, [
                'player' => $this->player,
                'team' => $this->team,
            ]);
        }

        if ($toSave) {
            if ($this->boat !== null) {
                $infos['boat'] = $this->boat;
            }
        }

        return $infos;
    }
}
