<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input as In;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class GamePenaltyCommand
 */
class GamePenaltyCommand extends Command
{
    protected static $defaultName = 'game:penalty';
    private $entityManager;

    /**
     * UserListCommand constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    /**
     * Configure command
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Enable/Disable penalty')
            ->addArgument('slug', In\InputArgument::REQUIRED, 'Game slug')
            ->addOption('enable', null, In\InputOption::VALUE_REQUIRED, 'Enable penalty (number of hours)')
            ->addOption('disable', null, In\InputOption::VALUE_NONE, 'Disable penalty')
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

        // Get params
        $slug = $input->getArgument('slug');
        $isEnable = $input->getOption('enable');
        $isDisable = $input->getOption('disable');

        // Check
        if (($isEnable && $isDisable) || (!$isEnable && !$isDisable)) {
            $console->error($isEnable.' '.$isDisable);
            $console->error('You must choice : enable OR disable penalty');

            return 3;
        }

        // Get game
        $repo = $this->entityManager->getRepository('App:Game');
        $game = $repo->findOneBy(['slug' => $slug]);
        if (!$game) {
            $console->error('Game not found');

            return 2;
        }

        if ($isEnable) {
            $hours = max(0, min(intval($input->getOption('enable')), 72));
            $msg = 'Penalty are set to '.$hours.' hours';
        } else {
            $hours = 0;
            $msg = 'Penalty are disabled';
        }

        // Save
        $game->setOption('penalty', $hours);
        $this->entityManager->flush();
        $console->success(sprintf('%s for game "%s" !', $msg, $game->getName()));

        return 0;
    }
}
