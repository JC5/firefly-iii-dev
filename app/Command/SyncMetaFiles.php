<?php
declare(strict_types=1);

namespace App\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Sync meta files over all repositories
 */
class SyncMetaFiles extends Command
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
            ->setName('ff3:sync-meta-files')
            ->setDescription('Sync meta files over all repositories.');
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
        $this->syncPrTemplates();
        return Command::SUCCESS;
    }

    private function syncPrTemplates(): void
    {
        $directories = $this->configuration['sites'];
        $template    = file_get_contents(__DIR__ . '/../../templates/pr.md');
        foreach ($directories as $directory) {
            $this->syncPrTemplateForDirectory(sprintf('%s/%s', $_ENV['FF3_ROOT'], $directory), $template);
        }
    }

    private function syncPrTemplateForDirectory(string $directory, string $template): void
    {
        echo sprintf('Syncing PR template for %s', $directory) . PHP_EOL;
        // remove all caps version
        $prTemplatePath = sprintf('%s/%s', $directory, '.github/PULL_REQUEST_TEMPLATE.md');
        if (file_exists($prTemplatePath)) {
            unlink($prTemplatePath);
            echo sprintf('Removed all-caps file in %s', $prTemplatePath) . PHP_EOL;
        }
        $prTemplatePath = sprintf('%s/%s', $directory, '.github/pull_request_template.md');
        if (!file_exists($prTemplatePath)) {
            file_put_contents($prTemplatePath, $template);
            echo sprintf('Saved new template in %s', $prTemplatePath) . PHP_EOL;
        }
    }


}
