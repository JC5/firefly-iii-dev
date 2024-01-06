<?php
declare(strict_types=1);


namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CleanupChangelog
 */
class CleanupChangelog extends Command
{
    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setName('ff3:changelog')
            ->setDescription('Update and parse changelogs.');
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
        $config = include(VARIABLES);
        $files  = [
            'FF3'  => sprintf('%s/changelog.md', $config['paths']['firefly_iii']),
            'Data' => sprintf('%s/changelog.md', $config['paths']['data']),
        ];

        foreach ($files as $key => $file) {
            $content = file_get_contents($file);

            // replace issue numbers (start of sentence)
            for ($i = 999; $i <= 9999; $i++) {
                $search  = '- #' . $i;
                $content = str_replace($search, '- [Issue ' . $i . '](https://github.com/firefly-iii/firefly-iii/issues/' . $i . ')', $content);
            }

            // replace issue numbers (the rest)
            for ($i = 999; $i <= 9999; $i++) {
                $search  = '#' . $i;
                $content = str_replace($search, '[issue ' . $i . '](https://github.com/firefly-iii/firefly-iii/issues/' . $i . ')', $content);
            }

            file_put_contents($file, $content);
            $output->writeln(sprintf('The changelog for %s has been parsed.', $key));
        }

        return 0;
    }

}
