<?php

namespace App\Controller;

use App\Entity\Game;
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
    public function ajaxGameInfo(Game $game, SerializerInterface $serializer): JsonResponse
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
    public function ajaxSetInfos(Game $game, Request $request): JsonResponse
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
                return new JsonResponse(['error' => 'Bad options']);
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
}
