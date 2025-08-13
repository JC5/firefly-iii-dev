<?php

namespace App\Support;

trait ExtractsChangelog
{
    protected function extractChangelog(string $path): string
    {

        $content        = file_get_contents($path);
        $lines          = explode("\n", $content);
        $changelogLines = [];
        $started        = false;
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, '## ') && false === $started && 0 === count($changelogLines)) {
                $started = true;
                continue;
            }
            if (str_starts_with($line, '## ') && true === $started) {
                break;
            }
            if ($started) {
                $changelogLines[] = $line;
            }
        }
        return trim(join("\n", $changelogLines));
    }
}