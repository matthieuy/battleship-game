<?php

namespace App;

use App\GameHelper\Boat;

/**
 * Class Boats
 */
class Boats
{
    private static $initLife = 0;

    /**
     * Get the boats list
     * @return array<Boat>
     */
    public static function getList(): array
    {
        $torpedo = (new Boat())
            ->setName('Torpedo')
            ->setNumber(4)
            ->setImgAlive([[5, 6], [41, 49]])
            ->setImgDead([[48, 56], [4, 12]]);

        $destroyer = (new Boat())
            ->setName('Destroyer')
            ->setNumber(3)
            ->setImgAlive([[13, 14, 15], [34, 42, 50]])
            ->setImgDead([[53, 54, 55], [3, 11, 19]]);

        $cruiser = (new Boat())
            ->setName('Cruiser')
            ->setNumber(2)
            ->setImgAlive([[21, 22, 23, 24], [27, 35, 43, 51]])
            ->setImgDead([[16, 45, 46, 47], [2, 10, 18, 26]]);

        $aircraft = (new Boat())
            ->setName('Aircraft')
            ->setNumber(1)
            ->setImgAlive([[29, 30, 31, 32, 40], [20, 28, 36, 44, 52]])
            ->setImgDead([[7, 8, 37, 38, 39], [1, 9, 17, 25, 33]]);

        return [
            $torpedo,
            $destroyer,
            $cruiser,
            $aircraft,
        ];
    }

    /**
     * Get dead image to replace alive
     * @param int $img        Alive img
     * @param int $lengthBoat Length of the boat
     *
     * @return int|null Dead image or null
     */
    public static function getDeadImg(int $img, int $lengthBoat): ?int
    {
        $boats = self::getList();
        /** @var Boat $boat */
        foreach ($boats as $boat) {
            if ($boat->getLength() === $lengthBoat) {
                return $boat->findDeadImg($img);
            }
        }

        return null;
    }

    /**
     * Get the init life
     * @return int
     */
    public static function getInitLife(): int
    {
        if (!self::$initLife) {
            $boats = self::getList();
            /** @var Boat $boat */
            foreach ($boats as $boat) {
                self::$initLife += $boat->getNumber() * $boat->getLength();
            }
        }

        return self::$initLife;
    }
}
