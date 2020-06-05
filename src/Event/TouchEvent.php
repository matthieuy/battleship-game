<?php

namespace App\Event;

use App\Entity\Game;
use App\Entity\Player;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class TouchEvent
 */
class TouchEvent extends Event
{
    // Type of touch
    public const TOUCH = 0;
    public const DISCOVERY = 1;
    public const DIRECTION = 2;
    public const SINK = 3;
    public const FATAL = 4;

    private Game $game;
    private ?Player $shooter;
    private Player $victim;
    private $boat;
    private int $type;

    /**
     * TouchEvent constructor.
     * @param Game         $game
     * @param array<mixed> $boat
     */
    public function __construct(Game $game, array $boat)
    {
        $this->game = $game;
        $this->boat = $boat;
    }

    /**
     * Set type of touch
     * @param int $type
     *
     * @return $this
     */
    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get Shooter
     * @return Player|null
     */
    public function getShooter(): ?Player
    {
        return $this->shooter;
    }

    /**
     * Set Shooter
     * @param Player|null $shooter
     *
     * @return $this
     */
    public function setShooter(?Player $shooter = null): self
    {
        $this->shooter = $shooter;

        return $this;
    }

    /**
     * Get Victim
     * @return Player
     */
    public function getVictim(): Player
    {
        return $this->victim;
    }

    /**
     * Set Victim
     * @param Player $victim
     *
     * @return $this
     */
    public function setVictim(Player $victim): self
    {
        $this->victim = $victim;

        return $this;
    }

    /**
     * Get Type
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Get Boat
     * @return array<mixed>
     */
    public function getBoat(): array
    {
        return $this->boat;
    }

    /**
     * Get Game
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }
}
