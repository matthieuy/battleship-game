<?php

namespace App\Event;

/**
 * Class MatchEvents
 */
final class MatchEvents
{
    /**
     * When a new game is create
     * Instance of Event\GameEvent
     */
    public const CREATE = 'match.create';

    /**
     * Before a game is delete
     * Instance of Event\GameEvent
     */
    public const BEFORE_DELETE = 'match.delete.before';

    /**
     * When a game is delete
     * Instance of Event\GameEvent
     */
    public const DELETE = 'match.delete';

    /**
     * When a game is launch
     * Instance of Event\GameEvent
     */
    public const LAUNCH = 'match.launch';
}
