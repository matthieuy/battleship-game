<?php

namespace App\Utils;

use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class MercureDispatcher
 */
class MercureDispatcher
{
    protected $router;
    protected $publisher;

    /**
     * MercureDispatcher constructor.
     * @param RouterInterface    $router
     * @param PublisherInterface $publisher
     */
    public function __construct(RouterInterface $router, PublisherInterface $publisher)
    {
        $this->router = $router;
        $this->publisher = $publisher;
    }

    /**
     * Dispatch data to a single topic
     * @param string        $route      The #Route name
     * @param array<string> $parameters The route parameters
     * @param array<mixed>  $data       Data to dispatch
     * @param array<string> $targets    Targets
     */
    public function dispatchData(string $route, array $parameters = [], array $data = [], array $targets = []): void
    {
        $topic = $this->router->generate($route, $parameters, RouterInterface::ABSOLUTE_URL);
        $json = json_encode([
            'topic' => $topic,
            'content' => $data,
        ], true);

        $update = new Update($topic, $json, $targets);
        $publisher = $this->publisher;
        $publisher($update);
    }
}
