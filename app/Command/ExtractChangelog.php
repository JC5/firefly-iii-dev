<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExtractChangelog
 */
class ExtractChangelog extends Command
{


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
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        include(VARIABLES);
        $changelog = sprintf('%s/changelog.md', $_ENV['FIREFLY_III_ROOT']);
        if(!file_exists($changelog)) {
            $output->writeln('Changelog does not exist.');
            return 1;
        }
        $content = file_get_contents($changelog);
        $lines   = explode("\n", $content);
        $changelogLines = [];
        $started = false;
        foreach($lines as $line) {
            $line = trim($line);
            if('' === $line) {
                continue;
            }
            if(str_starts_with($line, '## ') && false === $started && 0 === count($changelogLines)) {
                $started = true;
                continue;
            }
            if(str_starts_with( $line, '## ') && true === $started) {
                break;
            }
            if($started) {
                $changelogLines[] = $line;
            }
        }
        $log = join("\n", $changelogLines);

        echo $log;

        return 0;
    }

}
