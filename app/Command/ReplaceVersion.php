<?php
declare(strict_types=1);


namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReplaceVersion
 */
class ReplaceVersion extends Command
{
    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setName('ff3:version')
            ->setDescription('Replace version.')
            ->addArgument('version', InputArgument::REQUIRED, 'Which version?');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Exception
     *
     * TODO fix the search/replace.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        include(VARIABLES);
        $version     = (string)$input->getArgument('version');
        $fullVersion = $version;
        $output->writeln(sprintf('Will set version "%s" in firefly.php', $version));
        if ('develop' === $version) {
            $fullVersion = sprintf('develop/%s', date('Y-m-d'));
            $output->writeln(sprintf('For develop releases, the version gets a date: "%s"', $fullVersion));
        }
        if(str_starts_with($version, 'v')) {
            $fullVersion = substr($version, 1);
        }

        $configFile = sprintf('%s/config/firefly.php', $_ENV['FIREFLY_III_ROOT']);
        if (!file_exists($configFile)) {
            $output->writeln('Config file does not exist.');
            return 1;
        }
        $content  = file_get_contents($configFile);
        $newLines = [];
        $lines    = explode("\n", $content);
        foreach ($lines as $index => $line) {
            $trimmed = trim($line);
            if (str_starts_with($trimmed, "'version'")) {
                $newLines[] = sprintf("'version' => '%s',", $fullVersion);
                $output->writeln(sprintf('Replaced version in line #%d', $index));
            }
            if (!str_starts_with($trimmed, "'version'")) {
                $newLines[] = $line;
            }
        }
        $newContent = join("\n", $newLines);
        file_put_contents($configFile, $newContent);

        return 0;
    }

}
