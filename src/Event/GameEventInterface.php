<?php

namespace App\Event;

use App\Entity\Game;

/**
 * Interface GameEventInterface
 * @package App\Event
 */
interface GameEventInterface
{
    /**
     * Get game
     * @return Game
     */
    public function getGame();
}
