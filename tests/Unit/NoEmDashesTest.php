<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class NoEmDashesTest extends TestCase
{
    private const EM_DASH = "\u{2014}";

    /** @var list<string> */
    private const SCAN_DIRS = [
        'app',
        'resources/views',
        'database/data',
        'database/seeders',
        'tests',
    ];

    /** @var list<string> */
    private const SKIP_DIR_NAMES = [
        'vendor',
        'node_modules',
        'storage',
    ];

    /** @var list<string> */
    private const EXTENSIONS = [
        'php',
        'blade.php',
        'json',
        'md',
        'css',
        'js',
        'html',
        'txt',
    ];

    #[Test]
    public function repository_copy_does_not_contain_em_dashes(): void
    {
        $root = dirname(__DIR__, 2);
        $offenders = [];

        foreach (self::SCAN_DIRS as $relative) {
            $dir = $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
            if (! is_dir($dir)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
            );

            /** @var SplFileInfo $file */
            foreach ($iterator as $file) {
                if (! $file->isFile()) {
                    continue;
                }

                $path = $file->getPathname();
                foreach (self::SKIP_DIR_NAMES as $skip) {
                    if (str_contains($path, DIRECTORY_SEPARATOR.$skip.DIRECTORY_SEPARATOR)) {
                        continue 2;
                    }
                }

                $name = $file->getFilename();
                $okExt = false;
                foreach (self::EXTENSIONS as $ext) {
                    if (str_ends_with($name, '.'.$ext)) {
                        $okExt = true;
                        break;
                    }
                }
                if (! $okExt) {
                    continue;
                }

                $contents = @file_get_contents($path);
                if ($contents === false || ! str_contains($contents, self::EM_DASH)) {
                    continue;
                }

                $offenders[] = str_replace($root.DIRECTORY_SEPARATOR, '', $path);
            }
        }

        $this->assertSame(
            [],
            $offenders,
            "Em dashes (U+2014) are not allowed in Candidakuur copy. Offenders:\n".implode("\n", $offenders)
        );
    }
}
