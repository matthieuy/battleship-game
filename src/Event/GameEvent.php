<?php

namespace App\Event;

use App\Entity\Game;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class GameEvent
 */
class GameEvent extends Event implements GameEventInterface
{
    private $game;

    /**
     * GameEvent constructor
     * @param Game $game
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    /**
     * Get game
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }
}
