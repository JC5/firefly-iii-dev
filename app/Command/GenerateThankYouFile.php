<?php
declare(strict_types=1);

namespace App\Command;

use DateTime;
use Exception;
use League\CLImate\CLImate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class GenerateThankYouFile
 */
class GenerateThankYouFile extends Command
{
    private CLImate $climate;

    /**
     * CleanupCode constructor.
     *
     * @param string|null $name
     */
    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->climate = new CLImate();
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setName('ff3:thank-you')
            ->addArgument('product', InputArgument::OPTIONAL, 'For which product?')
            ->setDescription('Generate thank you file.');
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
        $product = (string)$input->getArgument('product');
        $config = include(VARIABLES);
        $path = $config['paths']['firefly_iii'];
        $toolName = 'Firefly III';
        $shortToolName = 'Firefly III';
        if ('data-importer' === $product) {
            $path = $config['paths']['data'];
            $toolName = 'the Firefly III Data Importer';
            $shortToolName = 'Firefly III Data Importer';
        }

        $command = sprintf('cd %s && git log', $path);
        $ignore = ['unknown', 'Scrutinizer Auto-Fixer', 'Dorigo', 'Sander Dorigo', 'James Cole', 'dependabot[bot]', 'mergify[bot]', 'github-actions', 'Sander D', 'JC5', 'root', 'github-actions[bot]'];
        $lines = [];
        $history = [];

        // execute command:
        exec($command, $lines);

        $previousAuthor = null;

        /** @var string $line */
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, 'Author:')) {
                $previousAuthor = trim(strip_tags(trim(str_replace('Author: ', '', $line))));
                $previousAuthor = str_replace('@', ' & ', $previousAuthor);
            }
            if (null !== $previousAuthor && !array_key_exists($previousAuthor, $history)) {
                $history[$previousAuthor] = time();
            }
            if (str_starts_with($line, 'Date:')) {
                $dateString = trim(str_replace('Date: ', '', $line));
                $dateObject = new DateTime($dateString);
                $epoch = $dateObject->getTimestamp();
                if ($epoch < $history[$previousAuthor]) {
                    $history[$previousAuthor] = $epoch;
                }
            }

        }
        $years = [];

        foreach ($history as $author => $timestamp) {
            $date = new DateTime();
            $date->setTimestamp($timestamp);
            $year = $date->format('Y');
            $years[$year] = $years[$year] ?? [];
            $years[$year][$timestamp] = $author;
        }


        krsort($years);

        $thanks = '# Thank you! :tada: :heart: :tada:' . PHP_EOL . PHP_EOL;
        $thanks .= sprintf('Over time, many people have contributed to %s. Their efforts are not always visible, but always remembered and appreciated.', $toolName) . PHP_EOL;
        $thanks .= sprintf('Please find below all the people who contributed to the %s code. Their names are mentioned in the year of their first contribution.', $shortToolName) . PHP_EOL;
        $thanks .= PHP_EOL;

        foreach ($years as $year => $authors) {
            krsort($authors);
            $thanks .= sprintf('## %s', $year);
            $thanks .= PHP_EOL;
            foreach ($authors as $key => $author) {
                if (!in_array($author, $ignore, true)) {
                    $thanks .= sprintf('- %s', $author);
                    $thanks .= PHP_EOL;
                }
            }
            $thanks .= PHP_EOL;
        }

        $thanks .= PHP_EOL;
        $thanks .= 'Thank you for all your support!';
        $thanks .= PHP_EOL;

        $path = sprintf('%s/THANKS.md', $config['paths']['firefly_iii']);
        file_put_contents($path, $thanks);

        return 0;
    }
}
