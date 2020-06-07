<?php

namespace App\GameHelper;

use App\Entity\Game;
use App\Entity\Player;
use App\Event\MatchEvents;
use App\Event\TouchEvent;
use App\Event\WeaponEvent;
use App\ImagesConstant;
use App\PointsConstants;
use App\Utils\MercureDispatcher;
use App\Weapons\WeaponInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class GameHelper
 */
class GameHelper
{
    private EventDispatcherInterface $eventDispatcher;
    private EntityManagerInterface $entityManager;
    private MercureDispatcher $mercureDispatcher;

    private ReturnBox $returnBox;
    private Game $game;

    /**
     * GameHelper constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManagerInterface   $entityManager
     * @param MercureDispatcher        $mercureDispatcher
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $entityManager,
        MercureDispatcher $mercureDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->mercureDispatcher = $mercureDispatcher;
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
        $this->game = $game;

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
        $boxList = $this->getBoxesToShoot($player, $x, $y, $weapon);

        // Do fire
        foreach ($boxList as $i => $box) {
            $result = $this->doFire($box, $player, $i === 0);

            // Error ?
            if ($result instanceof JsonResponse) {
                return $result;
            }
        }

        // realtime
        $result = $this->returnBox->getReturnInfos($this->game);
        $this->mercureDispatcher->dispatchData('match.display', ['slug' => $game->getSlug()], $result);

        // Save
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'infos' => $result,
        ]);
    }

    /**
     * Get list of box to shoot
     * @param Player               $player The shooter
     * @param int                  $x      X position
     * @param int                  $y      Y position
     * @param WeaponInterface|null $weapon Weapon
     *
     * @return array<Box>
     */
    private function getBoxesToShoot(Player $player, int $x, int $y, ?WeaponInterface $weapon = null): array
    {
        $boxList = [];

        if ($weapon) {
            $price = $weapon->getPrice();
            if ($player->getScore() >= $price) {
                $player->removeScore($price);
                $boxList = $weapon->getBoxes($this->game, $x, $y);

                $event = new WeaponEvent($this->game, $player, $weapon);
                $this->eventDispatcher->dispatch($event, MatchEvents::WEAPON);
            } else {
                $weapon = null;
            }
        }

        // No weapon (or price too expensive)
        if (!$weapon) {
            $boxList[] = Box::createFromGrid($x, $y, $this->game->getGrid());
        }
        $this->returnBox->setWeapon($weapon);


        return $boxList;
    }

    /**
     * Do a shoot
     * @param Box         $box      The box to shoot
     * @param Player|null $shooter  Shooter or null (for bonus type)
     * @param bool        $firstBox It is the first box we shoot ?
     *
     * @return bool|JsonResponse
     */
    private function doFire(Box $box, ?Player $shooter = null, bool $firstBox = false)
    {
        // Use weapon : add score to return on first shoot
        if ($firstBox && $this->returnBox->getWeapon()) {
            $box->setScore($shooter);
        }

        // Some check
        if (($shooter && $box->isSameTeam($shooter)) || $box->isAlreadyShoot() || $box->isOffzone($this->game->getSize())) {
            return false;
        }

        // Empty box => miss shoot
        if ($box->isEmpty()) {
            $box
                ->setImg(ImagesConstant::MISS)
                ->setShooter($shooter);
            $this->returnBox->addBox($box);
            $this->game->saveBox($box);

            return true;
        }

        return $this->touch($box, $shooter);
    }

    /**
     * Touch a boat
     * @param Box         $box
     * @param Player|null $shooter
     * @param bool        $isPenalty
     *
     * @return bool|JsonResponse
     */
    private function touch(Box $box, ?Player $shooter = null, bool $isPenalty = false)
    {
        // Update box
        $box->setShooter($shooter);

        // Get victim
        $victim = $this->game->getPlayerByPosition($box->getPlayer());
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
        $eventTouch = new TouchEvent($this->game, $boat);

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
        $this->game->saveBox($box);
        if ($isSink) {
            $this->returnBox->updateSink($this->game->getGrid(), $boat[1]);
            $this->game->updateSink($box, $boat[1]);
        }

        // Dispatch event
        $eventTouch
            ->setShooter($shooter)
            ->setVictim($victim);
        $this->eventDispatcher->dispatch($eventTouch, MatchEvents::TOUCH);

        return true;
    }
}
