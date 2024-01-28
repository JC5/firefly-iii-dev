<?php
declare(strict_types=1);

define('VARIABLES', sprintf('%s/variables.php', __DIR__));

use App\Command\BuildOldJs;
use App\Command\CleanupChangelog;
use App\Command\CleanupCode;
use App\Command\GenLanguageJson;
use Symfony\Component\Console\Application;
use App\Command\FixTranslationWarning;
use App\Command\ExtractChangelog;

require 'vendor/autoload.php';

$application = new Application('FF3 Support Tool', '3.0');

// commands:
$application->add(new CleanupChangelog);
$application->add(new CleanupCode);
$application->add(new FixTranslationWarning);
$application->add(new GenLanguageJson);
$application->add(new BuildOldJs());
$application->add(new ExtractChangelog());

$application->run();
