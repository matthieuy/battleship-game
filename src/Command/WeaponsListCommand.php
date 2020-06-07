<?php

namespace App\Command;

use App\Weapons\WeaponInterface;
use App\Weapons\WeaponRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class WeaponsListCommand
 */
class WeaponsListCommand extends Command
{
    protected static $defaultName = 'weapons:list';
    private WeaponRegistry $weaponRegistry;
    private TranslatorInterface $translator;

    /**
     * WeaponsListCommand constructor.
     * @param WeaponRegistry      $weaponRegistry
     * @param TranslatorInterface $translator
     */
    public function __construct(WeaponRegistry $weaponRegistry, TranslatorInterface $translator)
    {
        parent::__construct();

        $this->weaponRegistry = $weaponRegistry;
        $this->translator = $translator;
    }

    /**
     * Configure command
     */
    protected function configure(): void
    {
        $this->setDescription('Get weapons list');
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

        // Get weapons list
        $list = [];
        $weapons = $this->weaponRegistry->getAllWeapons();
        usort($weapons, function ($a, $b) {
            return $a->getPrice() - $b->getPrice();
        });

        /** @var WeaponInterface $weapon */
        foreach ($weapons as $weapon) {
            $list[] = [
                $weapon->getName(),
                $this->translator->trans($weapon->getName(), [], 'js'),
                $weapon->getPrice(),
                $weapon->isAiUseIt() ? 'X' : '',
                $weapon->isRotate() ? 'X' : '',
                $weapon->isShuffled() ? 'X' : '',
            ];
        }
        $headers = ['id', 'name', 'price', 'AI', 'Rotate', 'Shuffle'];

        $console->block('List of weapons :');
        $console->table($headers, $list);

        return 0;
    }
}
