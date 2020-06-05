<?php

namespace App\Event;

use App\Entity\Game;
use App\Entity\Player;
use App\Weapons\WeaponInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class WeaponEvent
 */
class WeaponEvent extends Event
{
    private Game $game;
    private Player $player;
    private WeaponInterface $weapon;

    /**
     * WeaponEvent constructor.
     * @param Game            $game
     * @param Player          $player
     * @param WeaponInterface $weapon
     */
    public function __construct(Game $game, Player $player, WeaponInterface $weapon)
    {
        $this->game = $game;
        $this->player = $player;
        $this->weapon = $weapon;
    }

    /**
     * Get Game
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }

    /**
     * Get Player
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * Get Weapon
     * @return WeaponInterface
     */
    public function getWeapon(): WeaponInterface
    {
        return $this->weapon;
    }
}
