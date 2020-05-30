<?php

namespace App\GameHelper;

use App\Entity\Game;
use App\Entity\Player;

/**
 * Class GridGenerator
 */
class GridGenerator
{
//    private $game;
    private $players;
    private $error;
    private $errorParameters = [];

    /**
     * GridGenerator constructor.
     * @param Game $game
     */
    public function __construct(Game $game)
    {
//        $this->game = $game;
        $this->players = $game->getPlayers();
    }

    /**
     * Check configuration
     * @return bool
     */
    public function checkConfiguration(): bool
    {
        // Get teams
        $teamList = [];
        foreach ($this->players as $player) {
            $teamList[$player->getTeam()] = true;
        }

        // Check number of team
        $nbTeams = count($teamList);
        if ($nbTeams <= 1) {
            $this->error = 'error_run_team';

            return false;
        }

        // Check team order
        for ($i = 1; $i <= $nbTeams; $i++) {
            if (!array_key_exists($i, $teamList)) {
                $this->error = 'error_run_emptyteam';
                $this->errorParameters = ['%team%' => $i];

                return false;
            }
        }

        // Human player
        $humanPlayer = $this->players->filter(function (Player $player) {
            return !$player->isAi();
        });
        if (!count($humanPlayer)) {
            $this->error = 'error_run_ai';

            return false;
        }

        return true;
    }

    /**
     * Get the current error (for translator)
     * @return array<string>
     */
    public function getCurentError(): array
    {
        return [
            'id' => $this->error,
            'parameters' => $this->errorParameters,
        ];
    }
}
