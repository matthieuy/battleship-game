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
    private $score = [];
    private $life = [];
    private $sinkInfo = [];
    private $isSink = false;
    private $dead = false;

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
    public static function createFromGrid(int $x, int $y, array $gridOrBox): Box
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
     * Is your boat ?
     * @param Player $player
     *
     * @return bool
     */
    public function isOwner(Player $player): bool
    {
        return $this->player === $player->getPosition();
    }

    /**
     * Is the same team
     * @param Player|null $player
     *
     * @return bool
     */
    public function isSameTeam(?Player $player = null): bool
    {
        return $player && $this->team === $player->getTeam();
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
     * Set new score
     * @param Player $player
     *
     * @return $this
     */
    public function setScore(Player $player): self
    {
        $this->score[$player->getPosition()] = $player->getScore();

        return $this;
    }

    /**
     * Set new life
     * @param Player $player
     *
     * @return $this
     */
    public function setLife(Player $player): self
    {
        $this->life[$player->getPosition()] = $player->getLife();
    }

    /**
     * Set Dead
     * @param bool $dead
     *
     * @return $this
     */
    public function setDead(bool $dead): self
    {
        $this->dead = $dead;

        return $this;
    }

    /**
     * Is out of the grid ?
     * @param int $gridSize
     *
     * @return bool
     */
    public function isOffzone(int $gridSize): bool
    {
        return $this->x < 0 || $this->y < 0 || $this->x >= $gridSize || $this->y >= $gridSize;
    }

    /**
     * Set boat as sink
     * @param bool $isSink
     *
     * @return $this
     */
    public function setSink(bool $isSink = true): self
    {
        $this->isSink = $isSink;

        return $this;
    }

    /**
     * Add sink infos
     * @param array<mixed> $infos
     *
     * @return $this
     */
    public function addSinkInfo(array $infos): self
    {
        $this->sinkInfo[] = $infos;

        return $this;
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
     * @param bool $toSave Get infos for save in DB
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
        if ($this->dead) {
            $infos['dead'] = $this->dead;
        }
        if (!$this->dead && $this->shoot !== null && $this->img > 0) {
            $infos['explose'] = true;
        }

        if ($toSave) {
            if ($this->boat !== null) {
                $infos['boat'] = $this->boat;
            }
        } else {
            if (!count($this->score)) {
                $infos['score'] = $this->score;
            }
            if (!count($this->life)) {
                $infos['life'] = $this->life;
            }
            if ($this->isSink) {
                $infos['sink'] = $this->sinkInfo;
            }
        }

        return $infos;
    }
}
