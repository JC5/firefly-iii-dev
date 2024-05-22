<?php
declare(strict_types=1);


namespace App\Command;

use Exception;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PostToGitter
 */
class PostToGitter extends Command
{
    private string $room = '!epdtwMcKTscMlFxeBi:gitter.im';

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setName('ff3:post-to-gitter ')
            ->setDescription('Posts to Gitter using a token.')
            ->addArgument('type', InputArgument::REQUIRED, 'Which issue type?');
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
        $output->writeln('Will post to Gitter.');
        $type = (string) $input->getArgument('type');
        if ('issue' === $type) {
            $this->postIssue($output);
            return 0;
        }
        $output->writeln(sprintf('Did not recognize issue type "%s", no post was made.', $type));
        return 0;
    }

    private function postIssue(OutputInterface $output): void
    {
        $output->writeln('Will post about an issue to Gitter.');
        $number     = (string) (getenv('ISSUE_NUMBER') ?? '');
        $repository = (string) (getenv('REPOSITORY') ?? '');
        $title      = (string) (getenv('ISSUE_TITLE') ?? '');
        $user       = (string) (getenv('ISSUE_USER') ?? '');
        $token      = (string) (getenv('GITTER_TOKEN') ?? '');

        if ('' === $number) {
            $output->writeln('No issue number provided.');
            return;
        }
        $output->writeln(sprintf('Issue number: #%s', $number));
        $output->writeln(sprintf('Issue title: %s', $title));
        $output->writeln(sprintf('Issue user: %s', $user));
        $output->writeln(sprintf('GitHub repository: %s', $repository));

        $full   = 'https://%s/_matrix/client/v3/rooms/%s/send/m.room.message/%s';
        $host   = 'gitter.ems.host';
        $client = new Client();

        $message     = sprintf('🤖 On GitHub, a new issue was opened by **%s**: "[%s](https://github.com/%s/issues/%s)"', $user, $title, $repository, $number);
        $messageHtml = sprintf('🤖 On GitHub, a new issue was opened by <b>%s</b>: "<a href="https://github.com/%s/issues/%s" title="%s">%s</a>"', $user, $repository, $number, $title, $title);


        $res = $client->put(sprintf($full, $host, $this->room, time()), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                "Accept"        => 'application/json',
            ],
            'body'    => json_encode(
                [
                    'body'           => $message,
                    'format'         => 'org.matrix.custom.html',
                    'formatted_body' => $messageHtml,
                    'msgtype'        => 'm.text',
                ]),
        ]);
        $output->writeln(sprintf('Result of PUT: HTTP %d', $res->getStatusCode()));
    }

}
