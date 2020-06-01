<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Player;
use App\Entity\User;
use App\Event\GameEvent;
use App\Event\MatchEvents;
use App\Form\GameType;
use App\GameHelper\GridGenerator;
use DateTime as DateTimeAlias;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class MatchController
 */
class MatchController extends AbstractController
{
    /**
     * Create a game
     * @Route(name="match.create", path="/create-game", methods={"GET", "POST"})
     *
     * @param Request                  $request    The game
     * @param EventDispatcherInterface $dispatcher Event dispatcher
     *
     * @return Response The vue
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
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"=true})
     *
     * @param Game $game The game
     *
     * @return Response The view
     */
    public function display(Game $game): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Variables to display on view
        $viewParams = [
            'game' => $game,
            'canDelete' => $this->isGranted('ROLE_ADMIN') || $game->isCreator($user),
            'inGame' => $game->getStatus() !== Game::STATUS_WAIT,
        ];

        // Waiting page
        if ($game->getStatus() === Game::STATUS_WAIT) {
            return $this->render('match/waiting.html.twig', $viewParams);
        }

        // Running page
        return $this->render('match/game.html.twig', $viewParams);
    }

    /**
     * Lautch the game
     * @Route(
     *     name="match.run",
     *     path="/game/{slug}/run",
     *     methods={"POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"="true"})
     *
     * @param Game                     $game
     * @param TranslatorInterface      $translator
     * @param EventDispatcherInterface $dispatcher
     *
     * @return JsonResponse
     */
    public function run(Game $game, TranslatorInterface $translator, EventDispatcherInterface $dispatcher): JsonResponse
    {
        // Check right
        /** @var User $user */
        $user = $this->getUser();
        if (!$game->isCreator($user) || $game->getStatus() !== Game::STATUS_WAIT) {
            return new JsonResponse(['error' => 'Not allowed']);
        }

        // Check configuration (same as vuejs code but in server side)
        $generator = new GridGenerator($game);
        if (!$generator->checkConfiguration()) {
            $error = $generator->getCurentError();
            $msg = $translator->trans($error['id'], $error['parameters'], 'js');

            return new JsonResponse([
                'success' => false,
                'error' => $msg,
            ]);
        }

        // Generate grid
        $result = $generator->generateGrid();
        if ($result === false) {
            // @todo Refactor code
            $error = $generator->getCurentError();
            $msg = $translator->trans($error['id'], $error['parameters']);

            return new JsonResponse([
                'success' => false,
                'error' => $msg,
            ]);
        }

        // Get tour
        $players = $game->getPlayersByTeam(1, false);
        $tour = [];
        foreach ($players as $player) {
            $tour[] = $player->getPosition();
        }

        // Save
        $now = new DateTimeAlias();
        $game
            ->setGrid($generator->getGrid())
            ->setTour($tour)
            ->setLastShoot($now)
            ->setRunAt($now)
            ->setStatus(Game::STATUS_RUN);
        $this->getDoctrine()->getManager()->flush();

        // Event
        $event = new GameEvent($game);
        $dispatcher->dispatch($event, MatchEvents::LAUNCH);

        // Result
        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * Delete a game (ajax)
     * @Route(
     *     name="match.delete",
     *     path="/game/{slug}/delete",
     *     methods={"POST"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"},
     *     options={"expose"="true"})
     *
     * @param Game                     $game            The game to delete
     * @param EventDispatcherInterface $eventDispatcher The event dispatcher
     *
     * @return JsonResponse
     */
    public function delete(Game $game, EventDispatcherInterface $eventDispatcher): JsonResponse
    {
        // Check right
        /** @var User $user */
        $user = $this->getUser();
        if (!$game->isCreator($user) && !$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['error' => 'Not allowed']);
        }

        // Event (before)
        $event = new GameEvent($game);
        $eventDispatcher->dispatch($event, MatchEvents::BEFORE_DELETE);

        // Delete
        $em = $this->getDoctrine()->getManager();
        $em->remove($game);
        $em->flush();

        // Event (after delete)
        $eventDispatcher->dispatch($event, MatchEvents::DELETE);

        // Result
        return new JsonResponse(['success' => true]);
    }
}
