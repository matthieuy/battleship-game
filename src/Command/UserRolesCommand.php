<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input as Input ;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class UserRolesCommand
 * @package App\Command
 */
class UserRolesCommand extends Command
{
    protected static $defaultName = 'user:roles';
    protected $roles = [
        'ROLE_CREATE_GAME',
        'ROLE_ADMIN',
        'ROLE_SUPER_ADMIN',
        'ROLE_ALLOWED_TO_SWITCH'
    ];
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    /**
     * Configure the commande
     */
    protected function configure()
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Add or remove roles')
            ->addOption('action', 'a', Input\InputOption::VALUE_OPTIONAL, 'add or remove')
            ->addArgument('username', Input\InputArgument::REQUIRED, 'Username')
            ->addOption('role', 'r',  Input\InputOption::VALUE_OPTIONAL, 'Role to add or remove')
        ;
    }

    /**
     * @param Input\InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(Input\InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Get arguments
        $action = $io->choice('Select the action to do', ['add', 'remove'], 0);
        $role = $io->choice('Select the role to '. $action, $this->roles);
        $username = $input->getArgument('username');

        // Get user
        /** @var UserRepository $repo */
        $repo = $this->entityManager->getRepository(User::class);
        $user = $repo->findOneBy(['username' => $username]);
        if (!$user) {
            $io->error(sprintf('User "%s" don\'t exist !', $username));
            return 1;
        }

        // Update roles
        $roles = $user->getRoles();
        if ($action === 'add') {
            $roles[] = $role;
        } else {
            if (($key = array_search($role, $roles)) !== false) {
                unset($roles[$key]);
            }
        }
        $user->setRoles($roles);

        // Save
        $this->entityManager->flush();
        $io->success(sprintf('User "%s" has roles : %s', $username, implode(', ', $roles)));

        return 0;
    }
}
