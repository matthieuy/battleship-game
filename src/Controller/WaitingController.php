<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Player;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class WaitingController
 * @package App\Controller
 */
class WaitingController extends AbstractController
{
    /**
     * Get game's infos
     * @param Game $game
     * @param SerializerInterface $serializer
     *
     * @Route(
     *     name="match.ajax.infos",
     *     path="/game/{slug}.json",
     *     methods={"GET"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
     * @return JsonResponse
     */
    public function getGameInfo(Game $game, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($game, 'json', ['groups' => 'infos']);

        return (new JsonResponse())->setJson($json);
    }

    /**
     * Edit infos (ajax)
     * @param Game $game
     * @param Request $request
     *
     * @Route(
     *     name="match.ajax.edit.infos",
     *     path="/game/{slug}/infos",
     *     methods={"POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
     * )
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

        // Response
        return new JsonResponse([
            'success' => true,
            'options' => $options,
            'value' => $value,
        ]);
    }

    /**
     * Set game options
     * @param Game $game
     * @param Request $request
     *
     * @Route(
     *     name="match.ajax.options",
     *     path="/game/{slug}/options",
     *     methods={"POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
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

        // Return
        return new JsonResponse([
            'success' => true,
            'option' => $optionName,
            'value' => $value,
        ]);
    }

    /**
     * Change color
     * @param Game $game
     * @param Request $request
     *
     * @Route(
     *     name="match.ajax.color",
     *     path="/game/{slug}/color",
     *     methods={"POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
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
     * @param Game $game
     * @param Request $request
     *
     * @Route(
     *     name="match.ajax.team",
     *     path="/game/{slug}/team",
     *     methods={"POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
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
     * @param Game $game
     * @param Request $request
     *
     * @Route(
     *     name="match.ajax.join",
     *     path="/game/{slug}/join",
     *     methods={"POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
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


        return new JsonResponse([
            'success' => $result,
        ]);
    }

    /**
     * Change order of the game
     * @param Game $game
     * @param Request $request
     *
     * @Route(
     *     name="match.ajax.order",
     *     path="/game/{slug}/order",
     *     methods={"POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
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

        return new JsonResponse(['success' => true]);
    }

    /**
     * Get the playerid from request or null
     * @param Game $game
     * @param Request $request
     * @return int|null
     */
    private function getPlayerId(Game $game, Request $request): ?int
    {
        /** @var User $user */
        $user = $this->getUser();
        return ($user && $game->isCreator($user)) ? $request->request->get('playerId') : null;
    }
}
