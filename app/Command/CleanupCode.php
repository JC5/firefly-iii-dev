<?php
declare(strict_types=1);

namespace App\Command;

use League\CLImate\CLImate;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CleanupCode
 */
class CleanupCode extends Command
{
    private CLImate $climate;
    private array   $extensions;
    private array   $paths;
    private array   $roots;

    /**
     * CleanupCode constructor.
     *
     * @param string|null $name
     */
    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $config           = require(VARIABLES);
        $this->climate    = new CLImate();
        $this->extensions = $config['cleanup']['extensions'];
        $paths            = $config['cleanup']['paths'];

        $this->roots = [];
        if ('' !== $config['paths']['firefly_iii']) {
            $this->roots[] = $config['paths']['firefly_iii'];

        }
        if ('' !== $config['paths']['data']) {
            $this->roots[] = $config['paths']['data'];
        }


        $this->paths = [];
        foreach ($paths as $path) {
            if('' !== $config['paths']['firefly_iii']) {
                $this->paths[] = sprintf('%s/%s', $config['paths']['firefly_iii'], $path);
            }
            if('' !== $config['paths']['data']) {
                $this->paths[] = sprintf('%s/%s', $config['paths']['data'], $path);
            }
        }
    }

    /**
     * @param string $name
     */
    function removeExecFlag(string $name): void
    {
        if (is_executable($name)) {
            exec(sprintf('chmod a-x %s', escapeshellarg($name)));
        }
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setName('ff3:code')
            ->setDescription('Update code and fix minor things.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        clearstatcache();
        $files = [];

        /*
         * Loop all paths and select files:
         */
        foreach ($this->paths as $path) {
            $count = 0;
            if (is_dir($path)) {
                $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
                foreach ($objects as $name => $object) {
                    if ($object->isFile()) {
                        // get extension:
                        $parts = explode('.', $name);
                        $ext   = $parts[count($parts) - 1];
                        if (\in_array($ext, $this->extensions, true)) {
                            $files[] = $name;
                            $count++;
                        }
                    }
                }
            }
            $this->climate->out(sprintf('Added %d files from %s', $count, $path));
        }

        /*
         * Also loop the root directory for files.
         *
         */
        foreach ($this->roots as $root) {
            if (is_dir($root)) {

                if ($dh = opendir($root)) {
                    $count = 0;
                    while (($file = readdir($dh)) !== false) {
                        $type = sprintf('%s/%s', $root, $file);
                        if ($type === 'file') {
                            $parts = explode('.', $file);
                            $ext   = $parts[count($parts) - 1];

                            if (in_array($ext, $this->extensions, true)) {
                                $files[] = sprintf('%s/%s', $root, $file);
                                $count++;
                            }
                        }
                    }
                    closedir($dh);
                    $this->climate->out(sprintf('Added %d files from %s', $count, $root));
                }
            }
        }
        $files = array_unique($files);

        $total = count($files);
        $this->climate->out(sprintf('[b] Found %d files in all paths.', count($files)));
        $i      = 0;
        $echoed = [];
        foreach ($files as $file) {
            $i++;
            echo '.';
            $pct          = (int)(($i / $total) * 100);
            $echoed[$pct] = $echoed[$pct] ?? false;
            if ($pct % 10 === 0 && false === $echoed[$pct]) {
                echo $pct . '%';
                $echoed[$pct] = true;
            }

            $this->checkForUTF($file);
            $this->removeExecFlag($file);
            $this->detectDoubleCopyright($file);
        }
        echo PHP_EOL;

        return 0;
    }

    /**
     * @param string $file
     */
    private function detectDoubleCopyright(string $file): void
    {
        $content        = file_get_contents($file);
        $countEmail     = substr_count($content, 'Copyright (c)');
        $countCopyright = substr_count($content, 'Affero');
        if ($countCopyright > 3 && $countEmail > 1) {
            $this->climate->out(sprintf('File %s has multiple copyright statements (%d and %d).', $file, $countEmail, $countCopyright));
        }
    }

    /**
     * @param string $name
     */
    private function checkForUTF(string $name): void
    {
        // get file content:
        $content = file_get_contents($name);
        // strlen > 0? Check if file is UTF8:
        $result = mb_detect_encoding($content, 'UTF-8', true);
        if (false === $result) {
            $this->climate->red(sprintf('Cannot detect encoding for file %s.', $name));
        }
        if ("ASCII" !== $result && 'UTF-8' !== $result) {
            $this->climate->red(sprintf('%s is %s instead of UTF8!', $name, var_export($result, true)));
        }
    }
}
