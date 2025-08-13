<?php
declare(strict_types=1);

namespace App\Command;

use App\Support\ExtractsChangelog;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExtractChangelog
 */
class ExtractChangelog extends Command
{
use ExtractsChangelog;

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setName('ff3:extract-changelog')
            ->setDescription('Extract latest changelog from changelog.md.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        include(VARIABLES);
        $changelog = sprintf('%s/changelog.md', $_ENV['FIREFLY_III_ROOT']);
        if (!file_exists($changelog)) {
            $output->writeln('Changelog does not exist.');
            return Command::FAILURE;
        }
        $content = $this->extractChangelog($changelog);

        echo $content;

        return Command::SUCCESS;
    }

}
