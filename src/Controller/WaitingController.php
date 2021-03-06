<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Player;
use App\Entity\User;
use App\Utils\MercureDispatcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class WaitingController
 */
class WaitingController extends AbstractController
{
    private $mercure;
    private $normalizer;

    /**
     * WaitingController constructor.
     * @param MercureDispatcher   $mercure
     * @param NormalizerInterface $normalizer
     */
    public function __construct(MercureDispatcher $mercure, NormalizerInterface $normalizer)
    {
        $this->mercure = $mercure;
        $this->normalizer = $normalizer;
    }

    /**
     * Get game's infos
     * @Route(
     *     name="match.ajax.infos",
     *     path="/game/{slug}.json",
     *     methods={"GET"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
     * @Route(
     *     name="match.ajax.infos.players",
     *     path="/game/{slug}-players.json",
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
     *
     * @param Game $game
     *
     * @return JsonResponse
     */
    public function getGameInfo(Game $game): JsonResponse
    {
        return new JsonResponse($this->normalizer->normalize($game, null, ['groups' => ['infos', 'players']]));
    }

    /**
     * Edit infos (ajax)
     * @Route(
     *     name="match.ajax.edit.infos",
     *     path="/game/{slug}/infos",
     *     methods={"POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
     *
     * @param Game    $game
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function setInfos(Game $game, Request $request): JsonResponse
    {
        // Check rights
        /** @var User $user */
        $user = $this->getUser();
        if (!$user || !$game->isCreator($user) || $game->getStatus() !== Game::STATUS_WAIT) {
            return new JsonResponse(['error' => 'Not allowed']);
        }

        // Get params
        $options = $request->request->get('options');
        $value = $request->request->get('value');

        // Edit infos
        switch ($options) {
            case 'size':
                $game->setSize($value);
                break;

            case 'maxplayers':
                $game
                    ->setMaxPlayer($value)
                    ->setSizeByPlayersNb($value);
                break;

            default:
                return new JsonResponse(['error' => 'Bad option']);
        }

        // Save
        $this->getDoctrine()->getManager()->flush();

        // Dispatch
        $this->dispatchUpdateGame($game);

        // Response
        return new JsonResponse([
            'success' => true,
            'options' => $options,
            'value' => $value,
        ]);
    }

    /**
     * Set game options
     * @Route(
     *     name="match.ajax.options",
     *     path="/game/{slug}/options",
     *     methods={"POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
     *
     * @param Game    $game
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function setOption(Game $game, Request $request): JsonResponse
    {
        // Check rights
        /** @var User $user */
        $user = $this->getUser();
        if (!$user || !$game->isCreator($user) || $game->getStatus() === Game::STATUS_END) {
            return new JsonResponse(['error' => 'Not allowed']);
        }

        // Get params
        $optionName = $request->request->get('option');
        $value = $request->request->get('value');
        if ($optionName === null || $value === null || !in_array($optionName, ['penalty', 'weapon', 'bonus'])) {
            return new JsonResponse(['error' => 'Bad option']);
        }

        // Convert option value in good format
        if ($optionName === 'penalty') {
            $value = max(0, min(intval($value), 72));
        } else {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        // Save
        $game->setOption($optionName, $value);
        $this->getDoctrine()->getManager()->flush();

        // Dispatch
        $this->dispatchUpdateGame($game);

        // Return
        return new JsonResponse([
            'success' => true,
            'option' => $optionName,
            'value' => $value,
        ]);
    }

    /**
     * Change color
     * @Route(
     *     name="match.ajax.color",
     *     path="/game/{slug}/color",
     *     methods={"POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
     *
     * @param Game    $game
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function setColor(Game $game, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($game->getStatus() === Game::STATUS_END) {
            return new JsonResponse(['error' => 'Not allowed']);
        }

        // Get params
        $playerId = $this->getPlayerId($game, $request);
        $color = str_replace('#', '', $request->request->get('color', '000000'));
        if (!preg_match('/^[a-f0-9]{6}/i', $color)) {
            return new JsonResponse(['error' => 'Wrong color']);
        }

        // Change color
        $repo = $this->getDoctrine()->getRepository('App:Player');
        $player = $repo->getPlayer($game, $user, $playerId);
        if ($player instanceof Player) {
            $player->setColor($color);
            $this->getDoctrine()->getManager()->flush();

            // Dispatch
            $this->dispatchUpdatePlayers($game);

            // Result
            return new JsonResponse([
                'success' => true,
                'color' => $player->getColor(),
                'playerId' => $player->getId(),
            ]);
        }

        return new JsonResponse(['error' => 'Player not found']);
    }

    /**
     * Change team
     * @Route(
     *     name="match.ajax.team",
     *     path="/game/{slug}/team",
     *     methods={"POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
     *
     * @param Game    $game
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function changeTeam(Game $game, Request $request): JsonResponse
    {
        // Check right
        /** @var User $user */
        $user = $this->getUser();
        if ($game->getStatus() !== Game::STATUS_WAIT) {
            return new JsonResponse(['error' => 'Not allowed']);
        }

        // Get params
        $playerId = $this->getPlayerId($game, $request);
        $team = max(1, min(12, intval($request->request->get('team'))));

        $repo = $this->getDoctrine()->getRepository('App:Player');
        $player = $repo->getPlayer($game, $user, $playerId);
        if ($player instanceof Player) {
            $player->setTeam($team);
            $this->getDoctrine()->getManager()->flush();

            // Dispatch
            $this->dispatchUpdatePlayers($game);

            // Result
            return new JsonResponse([
                'success' => true,
                'team' => $player->getTeam(),
                'playerId' => $player->getId(),
            ]);
        }

        return new JsonResponse(['error' => 'Player not found']);
    }

    /**
     * Join or leave a game
     * @Route(
     *     name="match.ajax.join",
     *     path="/game/{slug}/join",
     *     methods={"POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
     *
     * @param Game    $game
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function joinGame(Game $game, Request $request): JsonResponse
    {
        // Check right
        /** @var User $user */
        $user = $this->getUser();
        if ($game->getStatus() !== Game::STATUS_WAIT) {
            return new JsonResponse(['error' => 'Not allowed']);
        }

        $repo = $this->getDoctrine()->getRepository('App:Player');
        if ($request->request->getBoolean('join', false)) {
            // Join
            $isAi = ($game->isCreator($user) && $request->request->getBoolean('ai'));
            $result = $repo->joinGame($game, $user, $isAi);
        } else {
            // Leave
            $playerId = $this->getPlayerId($game, $request);
            $result = $repo->quitGame($game, $user, $playerId);
        }

        // Error (result contain message)
        if (is_string($result)) {
            return new JsonResponse(['error' => $result]);
        }

        // Dispatch
        $this->dispatchUpdatePlayers($game);

        // Result
        return new JsonResponse([
            'success' => $result,
        ]);
    }

    /**
     * Change order of the game
     * @Route(
     *     name="match.ajax.order",
     *     path="/game/{slug}/order",
     *     methods={"POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
     *
     * @param Game    $game
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function changeOrder(Game $game, Request $request): JsonResponse
    {
        // Check right
        /** @var User $user */
        $user = $this->getUser();
        if (!$game->isCreator($user) || $game->getStatus() !== Game::STATUS_WAIT) {
            return new JsonResponse(['error' => 'Not allowed']);
        }

        // Get player
        $position = $request->request->get('position', 0);
        $playerId = $this->getPlayerId($game, $request);
        $repo = $this->getDoctrine()->getRepository('App:Player');
        $player = $repo->getPlayer($game, $user, $playerId);

        // Move
        if ($player instanceof Player && $player->getPosition() !== $position) {
            $player->setPosition($position);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $em->refresh($game);
        }

        // Dispatch
        $this->dispatchUpdatePlayers($game);

        // Result
        return new JsonResponse(['success' => true]);
    }

    /**
     * Get the playerid from request or null
     * @param Game    $game
     * @param Request $request
     *
     * @return int|null
     */
    private function getPlayerId(Game $game, Request $request): ?int
    {
        /** @var User $user */
        $user = $this->getUser();

        return $user && $game->isCreator($user) ? $request->request->get('playerId') : null;
    }

    /**
     * Dispatch game info with mercure
     * @param Game $game
     */
    private function dispatchUpdateGame(Game $game): void
    {
        $infos = $this->normalizer->normalize($game, null, ['groups' => 'infos']);
        $this->mercure->dispatchData('match.ajax.infos', ['slug' => $game->getSlug()], $infos);
    }

    /**
     * Dispatch players list with mercure
     * @param Game $game
     */
    private function dispatchUpdatePlayers(Game $game): void
    {
        $players = $this->normalizer->normalize($game->getPlayers(), null, ['groups' => 'players']);
        $this->mercure->dispatchData('match.ajax.infos.players', ['slug' => $game->getSlug()], $players);
    }
}
