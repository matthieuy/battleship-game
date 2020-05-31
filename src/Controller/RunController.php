<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\GameHelper\GridGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
}
