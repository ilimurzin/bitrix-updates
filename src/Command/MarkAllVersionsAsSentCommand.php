<?php

declare(strict_types=1);

namespace App\Command;

use App\Store\Storage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:mark-all-as-sent',
    description: 'Mark all versions as sent (may be helpful at first run)',
)]
final class MarkAllVersionsAsSentCommand extends Command
{
    public function __construct(
        private readonly Storage $storage,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('dry-run')) {
            $io->note('Dry mode enabled');
        }

        $unsentVersions = $this->storage->getUnsent();

        if (!$input->getOption('dry-run')) {
            $this->storage->markAllAsSent();
        }

        $io->success(
            sprintf(
                'Marked %d versions as sent',
                count($unsentVersions)
            )
        );

        return Command::SUCCESS;
    }
}
