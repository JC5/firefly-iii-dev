<?php
declare(strict_types=1);


namespace App\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
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

            // do preg match on 3, 4 or 5 digits starting with #
            $re      = '/#\d{3,5}/m';
            $matches = [];
            preg_match_all($re, $content, $matches, PREG_SET_ORDER, 0);
            foreach ($matches as $match) {
                $issueFull = $match[0];
                $issue     = str_replace('#', '', $match[0]);
                // get from GitHub
                $url = sprintf('https://api.github.com/repos/firefly-iii/firefly-iii/issues/%s', $issue);

                $opts   = [
                    'headers' => [
                        'Accept'        => 'application/vnd.github+json',
                        'User-Agent'    => 'Firefly III changelog script/1.0',
                        'Authorization' => sprintf('Bearer %s', $config['gh_token']),
                    ],
                ];
                $client = new Client;
                try {
                    $res = $client->get($url, $opts);
                } catch (RequestException $e) {
                    $output->writeln(sprintf('Issue #%d is not an issue, perhaps a discussion?', $issue));
                    $res = null;
                }
                if (null !== $res) {
                    $body = (string)$res->getBody();
                    $json = json_decode($body, true);
                    $word = 'Issue';
                    if ($json['pull_request']['url'] ?? false) {
                        $word = 'PR';
                    }
                    $replace = sprintf('[%s %d](%s) (%s) reported by @%s', $word, $issue, $json['html_url'], $json['title'], $json['user']['login']);
                }
                if (null === $res) {
                    $replace = sprintf('[Issue %d](https://github.com/firefly-iii/firefly-iii/issues/%d)', $issue, $issue);
                }

                $content = str_replace($issueFull, $replace, $content);
                $output->writeln(sprintf('Parsed issue %s', $issueFull));
                sleep(2);
            }

            file_put_contents($file, $content);
            $output->writeln(sprintf('The changelog for %s has been parsed.', $key));
        }

        return 0;
    }

}
