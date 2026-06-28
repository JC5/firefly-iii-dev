<?php
declare(strict_types=1);

namespace App\Command;

use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Generate JSON files with translations in them,
 * Class GenLanguageJson
 */
#[AsCommand(name: 'ff3-json-translations')]
class GenLanguageJson extends Command
{
    private array           $configuration;
    private InputInterface  $input;
    private array           $langConfig;
    private OutputInterface $output;

    /**
     * GenLanguageJson constructor.
     *
     * @param string|null $name
     */
    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->configuration = require(VARIABLES);
        $file                = sprintf('%s/config/translations.php', $this->configuration['paths']['firefly_iii']);
        if (file_exists($file)) {
            $this->langConfig = require($file);
        }
        if (!file_exists($file)) {
            $this->langConfig = [];
        }
    }

    public function __invoke(): int
    {
        $version = (string)$this->input->getArgument('version');
        $paths   = $this->getStoragePaths($version);
        $result  = [];

        // loop all languages
        /** @var string $language */
        foreach ($this->langConfig['languages'] as $language) {
            $result[$language] = $this->processLanguage($language);
        }

        foreach ($result as $language => $content) {
            $this->storeLanguage($language, $version, $content, $paths);
        }
        return 0;
    }

    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->input  = $input;
        $this->output = $output;
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setName('ff3:json-translations')
            ->setDescription('Generate JSON files for language.')
            ->addArgument('version', InputArgument::REQUIRED, 'For which version?');
    }

    /**
     * @param string $file
     * @param string $key
     *
     * @return string
     */
    private function getEnglishString(string $file, string $key): string
    {
        $root      = $this->configuration['paths']['firefly_iii'];
        $eFileName = sprintf('%s/resources/lang/en_US/%s.php', $root, $file);
        if (!file_exists($eFileName) || !is_file($eFileName)) {
            return sprintf('(!%s.%s!)', $file, $key);
        }
        $loadLanguage = require($eFileName);
        if (!array_key_exists($key, $loadLanguage)) {
            return sprintf('(%s.%s)', $file, $key);
        }
        return $loadLanguage[$key];
    }

    /**
     * @param string $version
     *
     * @return array
     */
    private function getStoragePaths(string $version): array
    {
        $return = [];
        if ('v1' === $version) {
            $return['locales']     = sprintf('%s/resources/assets/v1/src/locales', $this->configuration['paths']['firefly_iii']);
            $return['locale_file'] = sprintf('%s/resources/assets/v1/src/locales/%%s.json', $this->configuration['paths']['firefly_iii']);
        }
        if ('v2' === $version) {
            $return['locales']     = sprintf('%s/public/v2/i18n', $this->configuration['paths']['firefly_iii']);
            $return['locale_file'] = sprintf('%s/public/v2/i18n/%%s.json', $this->configuration['paths']['firefly_iii']);
        }
        if ('v3' === $version) {
            $return['locales']     = sprintf('%s/public/v3/i18n', $this->configuration['paths']['firefly_iii']);
            $return['locale_file'] = sprintf('%s/public/v3/i18n/%%s.json', $this->configuration['paths']['firefly_iii']);
        }

        return $return;
    }

    private function processLanguage(string $language): array
    {
        $this->output->writeln(sprintf('processLanguage("%s")', $language));
        $version       = (string)$this->input->getArgument('version');
        $root          = $this->configuration['paths']['firefly_iii'];
        $fileDirectory = sprintf('%s/resources/lang/%s', $root, $language);
        $return        = [];

        if (!file_exists($fileDirectory) || !is_dir($fileDirectory)) {
            return $return;
        }
        foreach ($this->langConfig['json'][$version] as $expectedFile => $expectedStrings) {
            $return[$expectedFile] = $this->processLanguageFile($language, $expectedFile, $expectedStrings);
        }
        return $return;
    }

    private function processLanguageFile(string $language, string $file, array $strings): array
    {
        $return    = [];
        $strings   = array_unique($strings);
        $root      = $this->configuration['paths']['firefly_iii'];
        $eFileName = sprintf('%s/resources/lang/%s/%s.php', $root, $language, $file);
        if (!file_exists($eFileName) || !is_file($eFileName)) {
            return [];
        }
        $loadLanguage = require($eFileName);
        // loop the expected strings from each language file.
        foreach ($strings as $string) {
            $return[$string] = $this->processLanguageString($loadLanguage, $file, $string);
        }
        return $return;
    }

    /**
     * @param array  $translations
     * @param string $file
     * @param string $key
     *
     * @return string
     */
    private function processLanguageString(array $translations, string $file, string $key): string
    {
        $translation = $translations[$key] ?? false;

        if (false === $translation) {
            $translation = $this->getEnglishString($file, $key);
        }
        return (string)$translation;
    }

    /**
     * @param string $language
     * @param string $version
     * @param array  $content
     * @param array  $paths
     *
     * @return void
     */
    private function storeLanguage(string $language, string $version, array $content, array $paths): void
    {
        $this->output->writeln(sprintf('storeLanguage("%s", array, array)', $language));
        if (!array_key_exists('config', $content)) {
            $this->output->writeln(sprintf('No "config" key in content for language "%s". Skip it.', $language));
            return;
        }
        $code         = $content['config']['html_language'];
        $json         = json_encode($content, JSON_PRETTY_PRINT, 16);
        $destinations = [];

        if ('v1' === $version) {
            $destinations[] = sprintf($paths['locale_file'], $code);
        }
        if ('v2' === $version) {
            $destinations[] = sprintf($paths['locale_file'], $code);
            $destinations[] = sprintf($paths['locale_file'], $language);
        }
        if ('v3' === $version) {
            $destinations[] = sprintf($paths['locale_file'], $code);
            $destinations[] = sprintf($paths['locale_file'], $language);
        }

        foreach ($destinations as $destination) {
            file_put_contents($destination, $json);
        }
    }

}
