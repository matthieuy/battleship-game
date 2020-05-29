<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserRepository
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    /**
     * UserRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Get one AI available
     * @param array<int> $excludeIds List of IdUser to exclude
     *
     * @return User|null The AI or null
     */
    public function getAiavailable(array $excludeIds = []): ?User
    {
        // Count available AI
        $builder = $this->createQueryForAI($excludeIds);
        $builder->select('COUNT(user)');
        $nbAvailable = $builder->getQuery()->getSingleScalarResult();

        // None AI
        if (!$nbAvailable) {
            return null;
        }

        // Get random AI
        $builder = $this->createQueryForAI($excludeIds);
        $builder
            ->orderBy('user.username')
            ->setFirstResult(rand(0, $nbAvailable - 1))
            ->setMaxResults(1);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param UserInterface $user
     * @param string        $newEncodedPassword
     *
     * @throws ORM\ORMException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Load a user (for login)
     * @param string $username
     *
     * @return User
     */
    public function loadUserByUsername(string $username): User
    {
        // Query
        $builder = $this->createQueryBuilder('user');
        $builder
            ->select('user')
            ->where('user.username=:username')
            ->orWhere('user.email=:username')
            ->andWhere('user.ai=0')
            ->setParameter('username', $username)
            ->setMaxResults(1);
        $query = $builder->getQuery();

        // Cache
        $query
            ->useQueryCache(true)
            ->enableResultCache(10);

        try {
            $user = $query->getSingleResult();
        } catch (ORM\NoResultException $e) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found', $username), 0, $e);
        }

        return $user;
    }

    /**
     * Create a query for get AI available
     * @param array<int> $excludeIDs
     *
     * @return ORM\QueryBuilder
     */
    private function createQueryForAI(array $excludeIDs = []): ORM\QueryBuilder
    {
        $builder = $this->createQueryBuilder('user');
        $builder->where('user.ai=1');

        if (count($excludeIDs)) {
            $builder
                ->andWhere('user.id NOT IN (:exclude)')
                ->setParameter('exclude', $excludeIDs);
        }

        return $builder;
    }
}
