<?php

namespace App\Command;

use App\Support\ExtractsChangelog;
use Carbon\Carbon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateReleaseNotes extends Command
{
    use ExtractsChangelog;

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setName('ff3:generate-release-notes')
            ->addArgument('product', InputArgument::REQUIRED, 'For which product?')
            ->addArgument('version', InputArgument::REQUIRED, 'For which version?')
            ->setDescription('Update and parse changelogs.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws Exception
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config    = include(VARIABLES);
        $changelog = null;
        $product   = (string)$input->getArgument('product');
        $version   = (string)$input->getArgument('version');
        $path      = null;
        if ('firefly-iii' === $product) {
            $path      = $config['paths']['firefly_iii'];
            $changelog = sprintf('%s/changelog.md', $path);
        }
        if ('data-importer' === $product) {
            $path      = $config['paths']['data'];
            $changelog = sprintf('%s/changelog.md', $path);
        }
        if (null === $changelog) {
            $output->writeln(sprintf('Unknown product "%s".', $product));
            return Command::FAILURE;
        }

        if (!file_exists($changelog)) {
            $output->writeln(sprintf('Changelog for product "%s" does not exist.', $product));
            return Command::FAILURE;
        }
        $changelogContent = $this->extractChangelog($changelog);
        $templateName     = $this->getTemplateName($version);
        $templatePath     = sprintf('%s/.github/release-notes/%s', $path, $templateName);
        if (!file_exists($templatePath)) {
            $output->writeln(sprintf('Template "%s" does not exist.', $templatePath));
            return Command::FAILURE;
        }
        $templateContent = (string)file_get_contents($templatePath);
        $content         = $this->replaceTemplate($templateContent, $changelogContent, $version);

        echo $content;

        return Command::SUCCESS;
    }

    private function getTemplateName(string $version): string
    {
        if ('develop' === $version) {
            return 'develop.md';
        }
        if (str_contains($version, 'alpha')) {
            return 'alpha.md';
        }
        if (str_contains($version, 'beta')) {
            return 'beta.md';
        }

        return 'release.md';
    }

    private function replaceTemplate(string $templateContent, string $changelogContent, string $version): string
    {
        // replace date:
        $date            = Carbon::now('Europe/Amsterdam');
        $templateContent = str_replace('%date', $date->format('Y-m-d @ H:i'), $templateContent);

        // replace version:
        $templateContent = str_replace('%version', $version, $templateContent);

        // replace changelog:
        $templateContent = str_replace('%changelog', $changelogContent, $templateContent);

        return trim($templateContent);
    }
}