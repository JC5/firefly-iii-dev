<?php
declare(strict_types=1);

define('VARIABLES', sprintf('%s/variables.php', __DIR__));

use App\Command\CleanupChangelog;
use App\Command\CleanupCode;
use App\Command\GenerateReleaseNotes;
use App\Command\GenerateThankYouFile;
use App\Command\GenLanguageJson;
use Symfony\Component\Console\Application;
use App\Command\FixTranslationWarning;
use App\Command\ExtractChangelog;
use App\Command\ReplaceVersion;
use App\Command\SyncMetaFiles;
use App\Command\PostToGitter;

require 'vendor/autoload.php';


$application = new Application('FF3 Support Tool', '3.1');

// commands:
$application->addCommand(new CleanupChangelog);
$application->addCommand(new SyncMetaFiles);
$application->addCommand(new CleanupCode);
$application->addCommand(new GenerateReleaseNotes());
$application->addCommand(new FixTranslationWarning);
$application->addCommand(new GenLanguageJson);
$application->addCommand(new ExtractChangelog());
$application->addCommand(new ReplaceVersion());
$application->addCommand(new GenerateThankYouFile());
$application->addCommand(new PostToGitter());

$application->run();
