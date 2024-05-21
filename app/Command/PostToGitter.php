<?php
declare(strict_types=1);


namespace App\Command;

use Exception;
use GuzzleHttp\Client;
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
        $token = (string) (getenv('GITTER_TOKEN') ?? '');

        if ('' === $number) {
            $output->writeln('No issue number provided.');
            return 1;
        }
        $full  = 'https://%s/_matrix/client/v3/rooms/%s/send/m.room.message/%s';
        $host = 'gitter.ems.host';
        $room = '!LZoXfuHJSZYXgxfOOb:gitter.im';
        $client = new Client();

        $message = sprintf('🤖 New issue opened by [%s](https://github.com/%s): [%s](https://github.com/%s/issues/%s)', $user, $user, $title, $repository, $number);
        $messageHtml = sprintf('🤖 New issue opened by <a href="https://github.com/%s" title="%s">%s</a>: <a href="https://github.com/%s/issues/%s" title="%s">%s</a>', $user, $user, $user, $repository, $number, $title, $title);



        $res = $client->put(sprintf($full, $host, $room, time()), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                "Accept"        => 'application/json',
            ],
            'body'    => json_encode(
                [
                    'body'           => $message,
                    'format' => 'org.matrix.custom.html',
                    'formatted_body' => $messageHtml,
                    'msgtype'        => 'm.text',
                ]),
        ]);
        var_dump((string) $res->getStatusCode());

        echo 'Post to Gitter here.';

        return 0;
    }

}
