<?php
declare(strict_types=1);


namespace App\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PostToGitter
 */
class PostToGitter extends Command
{
    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setName('ff3:post-to-gitter')
            ->setDescription('Post to gitter');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws Exception
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config     = include(VARIABLES);
        $number     = (string) (getenv('ISSUE_NUMBER') ?? '');
        $repository = (string) (getenv('REPOSITORY') ?? '');
        $title      = (string) (getenv('ISSUE_TITLE') ?? '');
        $user       = (string) (getenv('ISSUE_USER') ?? '');

        if ('' === $number) {
            $output->writeln('No issue number provided.');
            return 1;
        }

        echo 'Post to Gitter here.';

        return 0;
    }

}
