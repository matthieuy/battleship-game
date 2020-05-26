<?php

namespace App;

/**
 * Class PointsConstants
 */
final class PointsConstants
{
    // First touch on a boat
    public const SCORE_DISCOVERY = 30;

    // Second touch on a boat
    public const SCORE_DIRECTION = 25;

    // Other touch on a boat
    public const SCORE_TOUCH = 10;

    // Sink a boat
    public const SCORE_SINK = 15;

    // Sink a player
    public const SCORE_FATAL = 50;
}
