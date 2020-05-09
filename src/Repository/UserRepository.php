<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM as ORM;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserRepository
 * @package App\Repository
 *
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
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param UserInterface $user
     * @param string $newEncodedPassword
     * @throws ORM\ORMException
     * @throws ORM\OptimisticLockException
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
     * @return User
     * @throws ORM\NonUniqueResultException
     */
    public function loadUserByUsername(string $username)
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
}
