<?php

namespace App;

/**
 * Class PointsConstants
 * @package App
 */
final class PointsConstants
{
    // First touch on a boat
    const SCORE_DISCOVERY = 30;

    // Second touch on a boat
    const SCORE_DIRECTION = 25;

    // Other touch on a boat
    const SCORE_TOUCH = 10;

    // Sink a boat
    const SCORE_SINK = 15;

    // Sink a player
    const SCORE_FATAL = 50;
}
