<?php

namespace App\GameHelper;

use App\Entity\Game;
use App\Entity\Player;
use App\Event\MatchEvents;
use App\Event\TouchEvent;
use App\Event\WeaponEvent;
use App\ImagesConstant;
use App\PointsConstants;
use App\Weapons\WeaponInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class GameHelper
 */
class GameHelper
{
    private EventDispatcherInterface $eventDispatcher;
    private EntityManager $entityManager;
    private ReturnBox $returnBox;

    /**
     * GameHelper constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManager            $entityManager
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, EntityManager $entityManager)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
    }

    /**
     * Do a shoot (call by human player only)
     * @param Game                 $game
     * @param Player               $player
     * @param int                  $x
     * @param int                  $y
     * @param WeaponInterface|null $weapon
     *
     * @return JsonResponse
     */
    public function shoot(Game $game, Player $player, int $x, int $y, ?WeaponInterface $weapon = null): JsonResponse
    {
        $this->returnBox = new ReturnBox();

        // Check box (only if no weapon else allow shoot on a box already shoot)
        $box = Box::createFromGrid($x, $y, $game->getGrid());
        if (!$box->isEmpty() && $weapon === null) {
            // Already shoot
            if ($box->isAlreadyShoot()) {
                return new JsonResponse(['error' => 'already_shoot']);
            }

            // Himself
            if ($box->isOwner($player)) {
                return new JsonResponse(['error' => 'shoot_your_boat']);
            }

            // Same team
            if ($box->isSameTeam($player)) {
                return new JsonResponse(['error' => 'team_shoot']);
            }
        }

        // Get boxes to shoot
        $boxList = $this->getBoxesToShoot($game, $player, $x, $y, $weapon);

        // Do fire
        foreach ($boxList as $i => $box) {
            $result = $this->doFire($game, $box, $player, $i === 0);

            // Error ?
            if ($result instanceof JsonResponse) {
                return $result;
            }
        }

        // Save
        $this->entityManager->flush();
    }

    /**
     * Get list of box to shoot
     * @param Game                 $game   The game
     * @param Player               $player The shooter
     * @param int                  $x      X position
     * @param int                  $y      Y position
     * @param WeaponInterface|null $weapon Weapon
     *
     * @return array<Box>
     */
    private function getBoxesToShoot(Game $game, Player $player, int $x, int $y, ?WeaponInterface $weapon = null): array
    {
        $boxList = [];

        if ($weapon) {
            $price = $weapon->getPrice();
            if ($player->getScore() >= $price) {
                $player->removeScore($price);
                $boxList = $weapon->getBoxes($game, $x, $y);

                $event = new WeaponEvent($game, $player, $weapon);
                $this->eventDispatcher->dispatch($event, MatchEvents::WEAPON);
            } else {
                $weapon = null;
            }
        }

        // No weapon (or price too expensive)
        if (!$weapon) {
            $boxList[] = Box::createFromGrid($x, $y, $game->getGrid());
        }
        $this->returnBox->setWeapon($weapon);


        return $boxList;
    }

    /**
     * Do a shoot
     * @param Game        $game     The game
     * @param Box         $box      The box to shoot
     * @param Player|null $shooter  Shooter or null (for bonus type)
     * @param bool        $firstBox It is the first box we shoot ?
     *
     * @return bool|JsonResponse
     */
    private function doFire(Game $game, Box $box, ?Player $shooter = null, bool $firstBox = false)
    {
        // Use weapon : add score to return on first shoot
        if ($firstBox && $this->returnBox->getWeapon()) {
            $box->setScore($shooter);
        }

        // Some check
        if (($shooter && $box->isSameTeam($shooter)) || $box->isAlreadyShoot() || $box->isOffzone($game->getSize())) {
            return false;
        }

        // Empty box => miss shoot
        if ($box->isEmpty()) {
            $box
                ->setImg(ImagesConstant::MISS)
                ->setShooter($shooter);
            $this->returnBox->addBox($box);
            $game->saveBox($box);

            return true;
        }

        return $this->touch($game, $box, $shooter);
    }

    /**
     * Touch a boat
     * @param Game        $game
     * @param Box         $box
     * @param Player|null $shooter
     * @param bool        $isPenalty
     *
     * @return bool|JsonResponse
     */
    private function touch(Game $game, Box $box, ?Player $shooter = null, bool $isPenalty = false)
    {
        // Update box
        $box->setShooter($shooter);

        // Get victim
        $victim = $game->getPlayerByPosition($box->getPlayer());
        if (!$victim) {
            return new JsonResponse(['error' => 'Player not found']);
        }

        // Get the boat
        $victimBoats = $victim->getBoats();
        $boatIndex = array_search($box->getBoat(), array_column($victimBoats, 0)); // Index 0 => boat number
        if ($boatIndex === false || !array_key_exists($boatIndex, $victimBoats)) {
            return new JsonResponse(['error' => 'error_boat404']);
        }
        $boat = $victimBoats[$boatIndex];

        // Remove a life and add a touch to the boat
        $victim->removeLife();
        $boat[2]++;
        $isSink = $boat[2] >= $boat[1]; // Touch >= length

        // Prepare event
        $eventTouch = new TouchEvent($game, $boat);

        // Calcul points
        if (!$victim->isAlive()) { // Fatal
            $points = PointsConstants::SCORE_FATAL;
            $eventTouch->setType(TouchEvent::FATAL);
        } elseif ($boat[2] === 1) { // Discovery
            $points = PointsConstants::SCORE_DISCOVERY;
            $eventTouch->setType(TouchEvent::DISCOVERY);
        } elseif ($boat[2] === 2) { // Direction
            $points = PointsConstants::SCORE_DIRECTION;
            $eventTouch->setType(TouchEvent::DIRECTION);
        } elseif ($isSink) { // Sink
            $points = PointsConstants::SCORE_SINK;
            $eventTouch->setType(TouchEvent::SINK);
        } else {
            $points = PointsConstants::SCORE_TOUCH;
            $eventTouch->setType(TouchEvent::TOUCH);
        }

        if (!$isPenalty && $shooter) {
            // Add score
            $shooter->addScore($points);
            if (!$shooter->isAi()) {
                $box->setScore($shooter);
            }
        }

        // Save
        $victimBoats[$boatIndex] = $boat;
        $victim->setBoats($victimBoats);
        $box
            ->setSink($isSink)
            ->setLife($victim);
        $this->returnBox->addBox($box);
        if ($isSink) {
            $grid = $game->updateSink($box, $boat[1]);
            $this->returnBox->updateSink($grid, $boat[1]);
            $game->setGrid($grid);
        }

        // Dispatch event
        $eventTouch
            ->setShooter($shooter)
            ->setVictim($victim);
        $this->eventDispatcher->dispatch($eventTouch, MatchEvents::TOUCH);

        return true;
    }
}
