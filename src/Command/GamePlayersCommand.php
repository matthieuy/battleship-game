<?php

namespace App\Command;

use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class GamePlayersCommand
 */
class GamePlayersCommand extends Command
{
    protected static $defaultName = 'game:players';
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
            ->setDescription('List player in a game')
            ->addArgument('slug', InputArgument::REQUIRED, 'Game slug')
        ;
    }

    /**
     * Execute command
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $console = new SymfonyStyle($input, $output);
        $slug = $input->getArgument('slug');

        // Get game
        $repo = $this->entityManager->getRepository('App:Game');
        $game = $repo->findOneBy(['slug' => $slug]);
        if (!$game) {
            $console->error('Game not found');

            return 2;
        }

        $players = $game->getPlayers();
        $list = [];
        /** @var Player $player */
        foreach ($players as $player) {
            $list[] = [
                $player->getId(),
                $player->getPosition(),
                $player->getName(),
                $player->isAi() ? 'X' : '',
                $player->getTeam(),
                $player->getUserId(),
            ];
        }
        $headers = [
            'ID',
            'Position',
            'Name',
            'IA',
            'Team',
            'UserID',
        ];

        $console->table($headers, $list);

        return 0;
    }
}
