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
    private InputInterface  $input;
    private OutputInterface $output;
    private string          $room = '!epdtwMcKTscMlFxeBi:gitter.im';

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
            ->setName('ff3:post-to-gitter ')
            ->setDescription('Posts to Gitter using a token.')
            ->addArgument('type', InputArgument::REQUIRED, 'Which issue type?');
    }

    public function __invoke(): int
    {
        $this->output->writeln('Will post to Gitter.');
        $type = (string)$this->input->getArgument('type');
        if ('issue' === $type) {
            $this->postIssue();
            return 0;
        }
        if ('pr' === $type) {
            $this->postPullRequest();
            return 0;
        }
        $this->output->writeln(sprintf('Did not recognize issue type "%s", no post was made.', $type));
        return 0;
    }

    private function postIssue(): void
    {
        $this->output->writeln('Will post about an issue to Gitter.');
        $number     = (string)(getenv('ISSUE_NUMBER') ?? '');
        $repository = (string)(getenv('REPOSITORY') ?? '');
        $title      = (string)(getenv('ISSUE_TITLE') ?? '');
        $user       = (string)(getenv('ISSUE_USER') ?? '');
        $token      = (string)(getenv('GITTER_TOKEN') ?? '');

        if ('' === $number) {
            $this->output->writeln('No issue number provided.');
            return;
        }
        $this->output->writeln(sprintf('Issue number: #%s', $number));
        $this->output->writeln(sprintf('Issue title: %s', $title));
        $this->output->writeln(sprintf('Issue user: %s', $user));
        $this->output->writeln(sprintf('GitHub repository: %s', $repository));

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
        $this->output->writeln(sprintf('Result of PUT: HTTP %d', $res->getStatusCode()));
    }

    private function postPullRequest(): void
    {
        $this->output->writeln('Will post about an issue to Gitter.');
        $number     = (string)(getenv('PR_NUMBER') ?? '');
        $repository = (string)(getenv('REPOSITORY') ?? '');
        $title      = (string)(getenv('PR_TITLE') ?? '');
        $user       = (string)(getenv('PR_USER') ?? '');
        $token      = (string)(getenv('GITTER_TOKEN') ?? '');

        if ('' === $number) {
            $this->output->writeln('No PR number provided.');
            return;
        }
        if ('dependabot' === $user || 'dependabot[bot]' === $user) {
            $this->output->writeln(sprintf('Will ignore user "%s".', $user));
            return;
        }
        $this->output->writeln(sprintf('PR number: #%s', $number));
        $this->output->writeln(sprintf('PR title: %s', $title));
        $this->output->writeln(sprintf('PR user: %s', $user));
        $this->output->writeln(sprintf('GitHub repository: %s', $repository));

        $full   = 'https://%s/_matrix/client/v3/rooms/%s/send/m.room.message/%s';
        $host   = 'gitter.ems.host';
        $client = new Client();

        $message     = sprintf('🤖 On GitHub, a new PR was opened by **%s**: "[%s](https://github.com/%s/pull/%s)"', $user, $title, $repository, $number);
        $messageHtml = sprintf('🤖 On GitHub, a new PR was opened by <b>%s</b>: "<a href="https://github.com/%s/pull/%s" title="%s">%s</a>"', $user, $repository, $number, $title, $title);


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
        $this->output->writeln(sprintf('Result of PUT: HTTP %d', $res->getStatusCode()));
    }

}
