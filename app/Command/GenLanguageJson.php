<?php
declare(strict_types=1);

namespace App\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate JSON files with translations in them,
 * Class GenLanguageJson
 */
class GenLanguageJson extends Command
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
            ->setName('ff3:json-translations')
            ->setDescription('Generate JSON files for language.')
            ->addArgument('version', InputArgument::REQUIRED, 'For which version?');
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
        $version     = (string)$input->getArgument('version');
        $localConfig = $this->getConfiguration($version);
        $root        = $this->configuration['paths']['firefly_iii'];

        $result = [];

        // loop all languages

        /** @var string $language */
        foreach ($this->configuration['languages'] as $language) {
            $result[$language] = [];
            //$output->writeln(sprintf('Now working on language %s', $language));
            // find directory:
            $fileDirectory = sprintf('%s/resources/lang/%s', $root, $language);
            if (!file_exists($fileDirectory) || !is_dir($fileDirectory)) {
                $output->writeln(sprintf('Directory "%s" does not exist.', $fileDirectory));
                continue;
            }
            //$output->writeln(sprintf('Found directory "%s"', $fileDirectory));

            // loop the expected language files:
            foreach ($localConfig['json'] as $expectedFile => $expectedStrings) {
                $expectedStrings = array_unique($expectedStrings);
                $eFileName       = sprintf('%s/resources/lang/%s/%s.php', $root, $language, $expectedFile);
                if (!file_exists($eFileName) || !is_file($eFileName)) {
                    $output->writeln(sprintf('File "%s" does not exist.', $eFileName));
                    exit;
                }
                //$output->writeln(sprintf('Found file "%s"', $eFileName));
                $loadLanguage = require($eFileName);
                // loop the expected strings from each language file.
                foreach ($expectedStrings as $expectedString) {
                    $translation = $loadLanguage[$expectedString] ?? false;
                    if (false !== $translation) {
                        //$output->writeln(sprintf('Found string "%s"', $expectedString));
                    }

                    if (false === $translation) {
                        #$output->writeln(sprintf('String "%s" from file "%s" in language "%s" could not be loaded.', $expectedString, $expectedFile, $language));
                        $translation = $this->getEnglishString($expectedFile, $expectedString);
                    }
                    $result[$language][$expectedFile][$expectedString] = trim($translation);
                }
            }
            //$output->writeln(sprintf('Finished with language %s', $language));
        }

        //$output->writeln('Done with all languages.');
        // the result can be put into JSON files.
        $storePath = sprintf($localConfig['locales'], $root);

        //$output->writeln('Looping results.');
        foreach ($result as $language => $content) {
              $output->writeln(sprintf('Now storing language %s', $language));
            // a bit of a catch 22. it's required this value is ALWAYS imported.
            $code        = $content['config']['html_language'];
            $destination = sprintf($localConfig['locale_file'], $code);
            $json        = json_encode($content, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT, 512);
            $copyright = file_get_contents(__DIR__ . '/../../js-copyright.txt');
            $copyright = sprintf($copyright, $language, date('Y'));

            if ('v1' === $version) {
                // basic but it works:
                file_put_contents($destination,  $json);
            }

            if('v2' === $version) {
                $json        = json_encode([$language => $content], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT, 512);
                $destination = sprintf($localConfig['locale_file'], $language);
                file_put_contents($destination, $json);
            }

            // v3 is no longer updated.
            if ('v3' === $version) {
                $destination = sprintf($localConfig['locale_file'], $language);
                $dir         = dirname($destination);
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                // make as module, not as file:
                $copyright  = '/*
 * index.js
 * Copyright (c) %s james@firefly-iii.org
 *
 * This file is part of Firefly III (https://github.com/firefly-iii).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */';
                $copyright = sprintf($copyright, date('Y'));
                $javascript = sprintf("%s\n\nexport default %s\n", $copyright, json_encode($content, JSON_PRETTY_PRINT));
                file_put_contents($destination, $javascript);
            }
        }
        return 0;
    }

    /**
     * @param string $expectedFile
     * @param string $expectedString
     *
     * @return mixed
     */
    private function getEnglishString(string $expectedFile, string $expectedString)
    {
        $root      = $this->configuration['paths']['firefly_iii'];
        $language  = 'en_US';
        $eFileName = sprintf('%s/resources/lang/%s/%s.php', $root, $language, $expectedFile);
        if (!file_exists($eFileName) || !is_file($eFileName)) {
            echo sprintf('Could not load fallback file "%s". It does not exist.', $eFileName) . "\n";
            exit;
        }
        $loadLanguage = require($eFileName);
        if (!isset($loadLanguage[$expectedString])) {
            echo sprintf('Could not find string "%s" in fallback file "%s".', $expectedString, $eFileName) . "\n";
        }
        if (isset($loadLanguage[$expectedString])) {
            return $loadLanguage[$expectedString];
        }

        return sprintf('(%s.%s)', $expectedFile, $expectedString);
    }

    /**
     * @param string $version
     *
     * @return array
     */
    private function getConfiguration(string $version): array
    {
        $return = [
            'json' => $this->configuration['json'][$version],
        ];
        if ('v1' === $version) {
            $return['locales']     = sprintf('%s/resources/assets/js/locales', $this->configuration['paths']['firefly_iii']);
            $return['locale_file'] = sprintf('%s/resources/assets/js/locales/%%s.json', $this->configuration['paths']['firefly_iii']);
        }
        if ('v2' === $version) {
            $return['locales']     = sprintf('%s/public/v2/i18n', $this->configuration['paths']['firefly_iii']);
            $return['locale_file'] = sprintf('%s/public/v2/i18n/%%s.json', $this->configuration['paths']['firefly_iii']);
        }
        if ('v3' === $version) {
            $return['locales']     = sprintf('%s/frontend/src/i18n/', $this->configuration['paths']['firefly_iii']);
            $return['locale_file'] = sprintf('%s/frontend/src/i18n/%%s/index.js', $this->configuration['paths']['firefly_iii']);
        }

        return $return;
    }

}
