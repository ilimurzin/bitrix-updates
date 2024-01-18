<?php

declare(strict_types=1);

namespace App\Command;

use App\Notify\TelegramNotificator;
use App\Store\Storage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:send-notifications',
    description: 'Send notifications about new versions'
)]
final class SendNotificationsCommand extends Command
{
    public function __construct(
        private readonly Storage $storage,
        private readonly TelegramNotificator $notificator,
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
            foreach ($unsentVersions as $version) {
                $this->notificator->notify($version);

                $this->storage->markAsSent($version);
            }
        }

        if (count($unsentVersions)) {
            $io->success(
                sprintf(
                    'Notified about %d new versions',
                    count($unsentVersions)
                )
            );
        } else {
            $io->success('No new versions');
        }

        return Command::SUCCESS;
    }
}
