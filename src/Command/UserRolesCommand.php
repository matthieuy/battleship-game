<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input as In ;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class UserRolesCommand
 */
class UserRolesCommand extends Command
{
    protected $roles = [
        'ROLE_CREATE_GAME',
        'ROLE_ADMIN',
        'ROLE_SUPER_ADMIN',
        'ROLE_ALLOWED_TO_SWITCH',
    ];
    protected $entityManager;
    protected static $defaultName = 'user:roles';

    /**
     * UserRolesCommand constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    /**
     * Configure the command
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Add or remove roles')
            ->addOption('action', 'a', In\InputOption::VALUE_OPTIONAL, 'add or remove')
            ->addArgument('username', In\InputArgument::REQUIRED, 'Username')
            ->addOption('role', 'r', In\InputOption::VALUE_OPTIONAL, 'Role to add or remove')
        ;
    }

    /**
     * Execute command
     * @param In\InputInterface $input
     * @param OutputInterface   $output
     *
     * @return int
     */
    protected function execute(In\InputInterface $input, OutputInterface $output): int
    {
        $console = new SymfonyStyle($input, $output);

        // Get arguments
        $action = $console->choice('Select the action to do', ['add', 'remove'], 0);
        $role = $console->choice('Select the role to '.$action, $this->roles);
        $username = $input->getArgument('username');

        // Get user
        /** @var UserRepository $repo */
        $repo = $this->entityManager->getRepository(User::class);
        $user = $repo->findOneBy(['username' => $username]);
        if (!$user) {
            $console->error(sprintf('User "%s" don\'t exist !', $username));

            return 1;
        }

        // Update roles
        $roles = $user->getRoles();
        if ($action === 'add') {
            $roles[] = $role;
        } elseif ($action === 'remove') {
            if (($key = array_search($role, $roles)) !== false) {
                unset($roles[$key]);
            }
        }
        $user->setRoles($roles);

        // Save
        $this->entityManager->flush();
        $console->success(sprintf('User "%s" has roles : %s', $username, implode(', ', $roles)));

        return 0;
    }
}
