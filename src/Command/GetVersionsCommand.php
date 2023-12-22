<?php

declare(strict_types=1);

namespace App\Command;

use App\Fetch\ModuleVersions;
use App\Fetch\Source;
use App\Store\Storage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:get-versions',
    description: 'Fetches new versions and save to database'
)]
final class GetVersionsCommand extends Command
{
    public function __construct(
        private readonly Source $source,
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

        $modulesVersions = $this->source->fetchModulesVersions();

        if ($input->getOption('dry-run')) {
            $io->success(
                sprintf(
                    'Fetched %d versions for %d modules',
                    array_sum(
                        array_map(
                            function (ModuleVersions $moduleVersions) {
                                return count($moduleVersions->versions);
                            },
                            $modulesVersions
                        )
                    ),
                    count($modulesVersions),
                )
            );

            return Command::SUCCESS;
        }

        $this->storage->store($modulesVersions);

        return Command::SUCCESS;
    }
}
