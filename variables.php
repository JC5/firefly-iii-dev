<?php
declare(strict_types=1);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();


$root     = $_ENV['FIREFLY_III_ROOT'] ?? '';
$dataRoot = $_ENV['DATA_IMPORTER_ROOT'] ?? '';
$allRoot  = '/ff3';

return [
    'gh_token'  => $_ENV['GH_TOKEN'],
    'paths'     => [
        'firefly_iii' => $root,
        'data'        => $dataRoot,
        'help'        => sprintf('%s/documentation/help', $allRoot),
    ],
    'sites' => [
        'data-importer',
        'documentation/api-docs-generator',
        'documentation/api-docs.firefly-iii.org',
        'documentation/docs.firefly-iii.org',
        'release/firefly-iii',
        'support/docker',
        'support/kubernetes',
        'tools-and-utilities/autosave',
        'tools-and-utilities/development-tools',
        'tools-and-utilities/product-manager',
        'tools-and-utilities/test-fixtures'
    ],
    'cleanup'   => [
        'extensions' => ['php', 'less', 'twig', 'gitkeep', 'gitignore', 'yml', 'xml', 'js'],
        'paths'      => ['.deploy', '.github', 'app', 'bootstrap', 'config', 'database', 'resources', 'routes', 'tests',],
    ],
];
