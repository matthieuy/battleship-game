<?php

namespace App\GameHelper;

use App\Boats;
use App\Entity\Game;
use App\Entity\Player;

/**
 * Class GridGenerator
 */
class GridGenerator
{
    protected $game;
    protected $players;
    protected $error;
    protected $errorParameters = [];
    protected $grid;

    /**
     * GridGenerator constructor.
     * @param Game $game
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
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
     * Generate the grid
     * @return bool Success or error
     *
     * @SuppressWarnings(PHPMD)
     */
    public function generateGrid(): bool
    {
        // Create a empty grid
        $gridSize = $this->game->getSize();
        $grid = [];
        for ($y = 0; $y < $gridSize; $y++) {
            $grid[$y] = [];
            for ($x = 0; $x < $gridSize; $x++) {
                $grid[$y][$x] = [];
            }
        }

        $boats = array_reverse(Boats::getList()); // List of boat with bigger first
        $boatNumber = 1; // Unique number of boat
        $boatsInfos = []; // Boats informations to save in DB

        // For all boats
        /** @var Boat $boat */
        foreach ($boats as $boat) {
            // For all boats (of this type)
            for ($iBoat = 0; $iBoat < $boat->getNumber(); $iBoat++) {
                // For all players
                foreach ($this->players as $player) {
                    // Init boat info
                    if (!array_key_exists($player->getPosition(), $boatsInfos)) {
                        $boatsInfos[$player->getPosition()] = [];
                    }

                    // Grid limit
                    $limitX = $gridSize - 1;
                    $limitY = $gridSize - 1;

                    // Random direction (define new limit with boat length and the delta to add for all boxes)
                    $direction = mt_rand(0, 1);
                    $dx = 0;
                    $dy = 0;
                    if ($direction) {
                        // Horizontal boat
                        $limitY -= $boat->getLength();
                        $dy = 1;
                    } else {
                        // Vertical boat
                        $limitX -= $boat->getLength();
                        $dx = 1;
                    }

                    $try = 0;
                    do {
                        $ok = true;

                        // Random position (the first box of the boat)
                        $x = mt_rand(0, $limitX);
                        $y = mt_rand(0, $limitY);
                        $currentX = $x;
                        $currentY = $y;

                        // For all box of the length boat
                        for ($iLength = 0; $iLength < $boat->getLength(); $iLength++) {
                            // Check if already a boat here
                            if (isset($grid[$currentY][$currentX]['img'])) {
                                $ok = false;
                                // phpcs:disable Squiz.WhiteSpace.ControlStructureSpacing.SpacingBeforeClose
                                break;

                            }

                            // Don't put 2 boats of the same player nearly
                            if ($try < 25) {
                                // Get near boxes
                                $nearBoxes = [];
                                if ($currentX > 0) {
                                    $nearBoxes[] = $grid[$currentY][$currentX-1]; // Left box
                                }
                                if ($currentX < $limitX) {
                                    $nearBoxes[] = $grid[$currentY][$currentX+1]; // Right box
                                }
                                if ($currentY > 0) {
                                    $nearBoxes[] = $grid[$currentY-1][$currentX]; // Top box
                                }
                                if ($currentY < $limitY) {
                                    $nearBoxes[] = $grid[$currentY+1][$currentX]; // Bottom box
                                }

                                // Check if we can put the boat for all boxes
                                foreach ($nearBoxes as $box) {
                                    // try<5 : no player nearly | try<=20 : no same team | try>20 : YOLO
                                    $isTeamBoat = isset($box['team']) && $box['team'] === $player->getTeam();
                                    if (($try < 5 && isset($box['player'])) || ($try <= 20 && $isTeamBoat)) {
                                        $ok = false;
                                        // phpcs:disable Squiz.WhiteSpace.ControlStructureSpacing.SpacingBeforeClose
                                        // phpcs:disable SlevomatCodingStandard.ControlStructures.JumpStatementsSpacing.IncorrectLinesCountAfterLastControlStructure
                                        break 2;
                                    }
                                }
                            }

                            // Goto the next box and continue check
                            $currentX += $dx;
                            $currentY += $dy;
                        }

                        // Protect against infinite loop
                        if (!$ok && $try > 50) {
                            $this->error = 'error_generate_loop';
                            $this->errorParameters = ['%try%' => $try];

                            return false;
                        }

                        $try++;
                    } while (!$ok);

                    // At this point, we have the coord of the first box of the boat, the direction
                    // and we know the boat have the place to be put here
                    $currentX = $x;
                    $currentY = $y;

                    // Put the boat on the grid
                    for ($iLength = 0; $iLength < $boat->getLength(); $iLength++) {
                        $grid[$currentY][$currentX] = [
                            'img' => $boat->getImg($iLength, $direction, false),
                            'player' => $player->getPosition(),
                            'team' => $player->getTeam(),
                            'boat' => $boatNumber,
                        ];

                        // Next box of this boat
                        $currentX += $dx;
                        $currentY += $dy;
                    }

                    // Boat info [number, length, touch]
                    $boatsInfos[$player->getPosition()][] = [$boatNumber, $boat->getLength(), 0];
                    $boatNumber++;
                }
            }
        }

        // Save players
        foreach ($this->players as $player) {
            $player
                ->setBoats($boatsInfos[$player->getPosition()])
                ->setScore(0)
                ->setLife(Boats::getInitLife());
        }

        $this->grid = $this->clearGrid($grid);

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

    /**
     * Get grid
     * @return array<mixed>
     */
    public function getGrid(): array
    {
        return $this->grid;
    }

    /**
     * Clear grid before save (keep only no empty box)
     * @param array<mixed> $grid
     *
     * @return array<mixed>
     */
    protected function clearGrid(array $grid): array
    {
        $gridSize = $this->game->getSize();
        $grid = $this->grid;
        $clearGrid = [];

        for ($y = 0; $y < $gridSize; $y++) {
            for ($x = 0; $x < $gridSize; $x++) {
                // phpcs:disable SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
                if (!count($grid[$y][$x])) {
                    $clearGrid[$y][$x] = $grid[$y][$x];
                }
            }
        }

        return $clearGrid;
    }
}
