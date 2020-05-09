<?php

namespace App\Event;


use App\Entity\Game;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class GameEvent
 * @package App\Event
 */
class GameEvent extends Event implements GameEventInterface
{
    private $game;

    /**
     * GameEvent constructor.
     * @param Game $game
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function getGame(): Game
    {
        return $this->game;
    }
}
