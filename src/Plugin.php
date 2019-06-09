<?php

namespace Helick\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

final class Plugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'post-install-cmd' => ['install'],
            'post-update-cmd'  => ['install'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        //
    }

    /**
     * Kick-off the installation.
     *
     * @return void
     */
    public function install(): void
    {
        $sourceDir = dirname(__DIR__, 1) . '/resources/stubs';
        $destDir   = dirname(__DIR__, 4);

        $this->createDirectories($destDir);

        $this->copyFiles($sourceDir, $destDir);
    }

    /**
     * Create necessary directories.
     *
     * @param string $destDir
     *
     * @return void
     */
    private function createDirectories(string $destDir): void
    {
        $directories = [
            '/bootstrap',
            '/bootstrap/cache',
            '/config',
            '/config/environments',
            '/web',
            '/web/content',
            '/web/content/mu-plugins',
            '/web/content/plugins',
            '/web/content/themes',
            '/web/content/uploads',
        ];

        $directories = array_filter($directories, function ($directory) use ($destDir) {
            return !is_dir($destDir . $directory);
        });

        foreach ($directories as $directory) {
            mkdir($destDir . $directory);
        }
    }

    /**
     * Copy necessary files.
     *
     * @param string $sourceDir
     * @param string $destDir
     *
     * @return void
     */
    private function copyFiles(string $sourceDir, string $destDir): void
    {
        $files = [
            '/bootstrap/cache/mu-plugins.php',
            '/bootstrap/cache/required-mu-plugins.php',
            '/config/environments/development.php',
            '/config/environments/staging.php',
            '/config/application.php',
            '/web/content/mu-plugins/autoloader.php',
            '/web/index.php',
            '/web/wp-config.php',
            '/.gitignore',
            '/.env.example',
            '/wp-cli.yml',
        ];

        $files = array_filter($files, function ($file) use ($destDir) {
            return !file_exists($destDir . $file);
        });

        foreach ($files as $file) {
            copy($sourceDir . $file . '.stub', $destDir . $file);
        }
    }
}
