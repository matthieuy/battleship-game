<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class UserListCommand
 * @package App\Command
 */
class UserListCommand extends Command
{
    protected static $defaultName = 'user:list';
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * UserListCommand constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setDescription('Display user list')
        ;
    }

    /**
     * Execute the commande
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Get users
        $repo = $this->entityManager->getRepository(User::class);
        $users = $repo->findAll();

        $list = [];
        foreach ($users as $user) {
            $list[] = [
                $user->getId(),
                $user->getUsername(),
                $user->isAi() ? 'X' : ' ',
                $user->getSlug(),
                $user->isAi() ? ' ' : $user->getEmail(),
                implode(', ', $user->getRoles()),
            ];
        }
        $headers = ['ID', 'Username', 'AI', 'Slug', 'E-mail', 'Roles'];

        $io->table($headers, $list);

        return 0;
    }
}
