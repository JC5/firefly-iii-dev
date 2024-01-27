<?php
declare(strict_types=1);

namespace App\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Build the old JS
 */
class BuildOldJs extends Command
{
    private array $configuration;

    /**
     * GenLanguageJson constructor.
     *
     * @param string|null $name
     */
    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->configuration = require(VARIABLES);
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setName('ff3:old-js')
            ->setDescription('Build old JS files.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        include(VARIABLES);
        $lines = [];
        exec(sprintf('FIREFLY_III_ROOT=%s ./v1-js/build.sh', $_ENV['FIREFLY_III_ROOT']), $lines);
        // here we are
        foreach ($lines as $line) {
            $output->writeln($line);
        }
        return 0;
    }


}
