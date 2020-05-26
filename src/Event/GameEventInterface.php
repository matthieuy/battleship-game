<?php

namespace App\Event;

use App\Entity\Game;

/**
 * Interface GameEventInterface
 */
interface GameEventInterface
{
    /**
     * Get game
     * @return Game
     */
    public function getGame(): Game;
}
