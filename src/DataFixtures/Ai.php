<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class Ai
 * @package App\DataFixtures
 */
class Ai extends Fixture implements OrderedFixtureInterface
{
    private $aiList = [
        'Edward Teach',
        'Roberto Cofresi',
        'Peter Easton',
        'Olivier Levasseur',
        'Mary Read',
        'John Roberts',
        'Klaus Stortebeker',
        'John Silver',
        'Edward England',
        'John Halsey',
        'Jean Lafitte',
        'Arudj Reis',
    ];

    /**
     * Load data fixtures
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->aiList as $name) {
            $ai = new User();
            $ai
                ->setAi(true)
                ->setUsername($name)
                ->setEmail(str_replace(' ', '', $name) . '@ai')
                ->setPassword($name);

            $manager->persist($ai);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
