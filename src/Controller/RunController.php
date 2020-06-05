<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\GameHelper\GameHelper;
use App\GameHelper\GridGenerator;
use App\Weapons\WeaponRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class RunController
 */
class RunController extends AbstractController
{
    /**
     * Load game
     * @Route(
     *     name="match.load",
     *     path="/game/{slug}/load.json",
     *     methods={"GET"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
     *
     * @param Game                $game
     * @param NormalizerInterface $normalizer
     *
     * @return JsonResponse
     */
    public function load(Game $game, NormalizerInterface $normalizer): JsonResponse
    {
        // Get game's infos
        $infos = $normalizer->normalize($game, null, ['groups' => ['run', 'players']]);
        $infos = array_merge($infos, [
            'boxSize' => 20, // @todo Get from user options
        ]);

        // Players list
        /** @var User $user */
        $user = $this->getUser();
        $mePlayer = $game->getPlayerByUser($user);
        foreach ($game->getPlayers() as $player) {
            // phpcs:disable SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
            if ($player->getUserId() === $this->getUser()->getId()) {
                $infos['players'][$player->getPosition()]['me'] = true;
            }
        }

        // Grid
        $generator = new GridGenerator($game);
        $infos['grid'] = $generator->getGridForPlayer($mePlayer);

        return new JsonResponse($infos);
    }

    /**
     * Do a shoot
     * @Route(
     *     name="match.shoot",
     *     path="/game/{slug}/shoot",
     *     methods={"POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
     *
     * @param Game           $game
     * @param Request        $request
     * @param WeaponRegistry $weaponRegistry
     * @param GameHelper     $gameHelper
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * @return JsonResponse
     */
    public function shoot(Game $game, Request $request, WeaponRegistry $weaponRegistry, GameHelper $gameHelper): JsonResponse
    {
        if ($game->isFinished()) {
            return new JsonResponse(['error' => 'gameover']);
        }

        // Get coord
        $x = $request->request->get('x');
        $y = $request->request->get('y');
        if ($x === null || $y === null || $x < 0 || $y < 0 || $x >= $game->getSize() || $y >= $game->getSize()) {
            return new JsonResponse(['error' => 'error_shoot']);
        }

        // Get player
        /** @var User $user */
        $user = $this->getUser();
        $player = $game->getPlayerByUser($user);
        if (!$player || !in_array($player->getPosition(), $game->getTour())) {
            return new JsonResponse(['error' => 'error_tour']);
        }

        // Get weapon
        $weapon = null;
        $weaponName = $request->request->get('weapon');
        if ($weaponName) {
            $weapon = $weaponRegistry->getWeapon($weaponName);
            $weapon->setNumberRotate($request->request->get('rotate', 0));
        }

        // Do the shoot
        $gameHelper->shoot($game, $player, $x, $y, $weapon);

        // Result
        return new JsonResponse(['success' => true]);
    }
}
