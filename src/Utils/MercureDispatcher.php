<?php

namespace App\Utils;

use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class MercureDispatcher
 */
class MercureDispatcher
{
    protected $router;
    private $messageBus;

    /**
     * MercureDispatcher constructor.
     * @param RouterInterface     $router
     * @param MessageBusInterface $messageBus
     */
    public function __construct(RouterInterface $router, MessageBusInterface $messageBus)
    {
        $this->router = $router;
        $this->messageBus = $messageBus;
    }

    /**
     * Dispatch data to a single topic
     * @param string        $route      The #Route name
     * @param array<string> $parameters The route parameters
     * @param array<mixed>  $data       Data to dispatch
     */
    public function dispatchData(string $route, array $parameters = [], array $data = []): void
    {
        $topic = $this->router->generate($route, $parameters, RouterInterface::ABSOLUTE_URL);
        $json = json_encode([
            'topic' => $topic,
            'content' => $data,
        ], true);

        $update = new Update($topic, $json);
        $this->messageBus->dispatch($update);
    }
}
