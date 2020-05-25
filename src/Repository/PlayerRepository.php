<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Player;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository
{
    /**
     * PlayerRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    /**
     * Join a game
     * @param Game $game
     * @param UserInterface|User $user
     * @param bool $ai
     * @return bool|string
     */
    public function joinGame(Game $game, UserInterface $user, $ai = false)
    {
        // Game is full
        $players = $game->getPlayers();
        if (count($players) >= $game->getMaxPlayer()) {
            return 'Could not join : game is full';
        }

        if ($ai) {

        } else {
            foreach ($players as $player) {
                if ($player->getUser()->getId() === $user->getId()) {
                    return 'You are already in the game';
                }
            }
        }

        // Create player
        $player = new Player();
        $player
            ->setUser($user)
            ->setTeam($this->getLastTeam($game))
            ->setName($user->getUsername())
            ->setColor($this->randomColor());
        $game->addPlayer($player);

        // Persist
        try {
            $em = $this->getEntityManager();
            $em->persist($player);
            $em->flush();
        } catch (\Exception $e) {
            return 'Error with persist data';
        }

        return true;
    }

    /**
     * Get the last team number
     * @param Game $game The game
     * @return integer The last team number
     */
    public function getLastTeam(Game $game)
    {
        $builder = $this->createQueryBuilder('player');
        $expr = new Expr();
        $builder
            ->select($expr->max('player.team'))
            ->where('player.game=:game')
            ->setParameter('game', $game)
            ;
        $result = $builder->getQuery()->getSingleResult();
        if (is_array($result)) {
            $result = array_shift($result);
        }
        $result++;

        return $result;
    }

    /**
     * Get a player
     * @param Game $game
     * @param User $user
     * @param int|null $playerId
     * @return Player|null
     */
    public function getPlayer(Game $game, User $user, ?int $playerId = null): ?Player
    {
        $builder = $this->createQueryPlayer($game, $user, $playerId);
        $builder
            ->select('player')
            ->setMaxResults(1);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * Generate a hexa color
     * @return string
     */
    private function randomColor()
    {
        $string = str_split('0123456789ABCDEF');
        $color = '';
        for ($i=0; $i < 6; $i++) {
            $r = (int) floor(mt_rand(0, 15));
            $color .= $string[$r];
        }

        return $color;
    }

    /**
     * Create a query with the playerId (if defined) or the current user
     * @param Game $game
     * @param User $user
     * @param int|null $playerId
     * @return QueryBuilder
     */
    private function createQueryPlayer(Game $game, User $user, int $playerId = null): QueryBuilder
    {
        $builder = $this->createQueryBuilder('player');
        $builder
            ->where('player.game=:game')
            ->setParameter('game', $game);

        if ($playerId) {
            $builder
                ->andWhere('player.id=:playerId')
                ->setParameter('playerId', $playerId);
        } else {
            $builder
                ->andWhere('player.user=:user')
                ->setParameter('user', $user);
        }

        return $builder;
    }
}
