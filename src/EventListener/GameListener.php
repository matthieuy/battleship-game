<?php

namespace App\EventListener;

use App\Event\GameEvent;
use App\Event\MatchEvents;
use App\Utils\MercureDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class GameListener
 */
class GameListener implements EventSubscriberInterface
{
    private $mercureDispatcher;
    private $router;

    /**
     * GameListener constructor.
     * @param MercureDispatcher $mercureDispatcher
     * @param RouterInterface   $router
     */
    public function __construct(MercureDispatcher $mercureDispatcher, RouterInterface $router)
    {
        $this->mercureDispatcher = $mercureDispatcher;
        $this->router = $router;
    }

    /**
     * Get subscribe event
     * @return array<string>
     *
     * phpcs:disable SlevomatCodingStandard.Classes.ClassStructure.IncorrectGroupOrder
     */
    public static function getSubscribedEvents(): array
    {
        return [
            MatchEvents::DELETE => 'onDelete',
            MatchEvents::LAUNCH => 'onLaunch',
        ];
    }

    /**
     * When delete a game
     * @param GameEvent $event
     */
    public function onDelete(GameEvent $event): void
    {
        // Dispatch redirect (on waiting page)
        $redirect = ['redirect' => $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL)];
        $this->mercureDispatcher->dispatchData('match.ajax.infos', ['slug' => $event->getGame()->getSlug()], $redirect);
    }

    /**
     * When launch game
     * @param GameEvent $event
     */
    public function onLaunch(GameEvent $event): void
    {
        // Dispatch redirect (on waiting page)
        $routeParameters = ['slug' => $event->getGame()->getSlug()];
        $redirect = ['redirect' => $this->router->generate('match.display', $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL)];
        $this->mercureDispatcher->dispatchData('match.ajax.infos', ['slug' => $event->getGame()->getSlug()], $redirect);
    }
}
