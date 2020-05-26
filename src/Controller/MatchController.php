<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Player;
use App\Event\GameEvent;
use App\Event\MatchEvents;
use App\Form\GameType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MatchController
 */
class MatchController extends AbstractController
{
    /**
     * Create a game
     * @Route(name="match.create", path="/create-game", methods={"GET", "POST"})
     *
     * @param Request                  $request
     * @param EventDispatcherInterface $dispatcher
     *
     * @return Response
     */
    public function create(Request $request, EventDispatcherInterface $dispatcher): Response
    {
        // Allow to create game
        if (!$this->isGranted('ROLE_CREATE_GAME')) {
            $this->addFlash('error', "error_allow_create");

            return $this->redirectToRoute('homepage');
        }

        // Create a new game
        $game = new Game();

        // Form
        $form = $this->createForm(GameType::class, $game);

        // Form request
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist
            $em = $this->getDoctrine()->getManager();
            $em->persist($game);
            $em->flush();

            // Join the game
            /** @var App\Repository\PlayerRepository $repo */
            $repo = $em->getRepository(Player::class);
            $result = $repo->joinGame($game, $this->getUser());
            if (is_string($result)) {
                $this->addFlash('error', $result);
            }

            // Event
            $event = new GameEvent($game);
            $dispatcher->dispatch($event, MatchEvents::CREATE);

            // Redirect
            return $this->redirectToRoute('match.display', [
                'slug' => $game->getSlug(),
            ]);
        }

        // View
        return $this->render('match/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Display a game
     * @Route(
     *     name="match.display",
     *     path="/game/{slug}",
     *     methods={"GET"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"})
     *
     * @param Game $game
     *
     * @return Response
     */
    public function display(Game $game): Response
    {
        // Waiting page
        if ($game->getStatus() === Game::STATUS_WAIT) {
            return $this->render('match/waiting.html.twig', [
                'game' => $game,
            ]);
        }

        return new Response();
    }
}
