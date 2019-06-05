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
        $source = dirname(__DIR__, 1) . '/resources/stubs';
        $dest   = dirname(__DIR__, 4) . '/web';

        $this->createDirectories($dest);

        $this->copyFiles($source, $dest);
    }

    /**
     * Create necessary directories.
     *
     * @param string $dest
     *
     * @return void
     */
    private function createDirectories(string $dest): void
    {
        $directories = [
            $dest,
            $dest . '/content',
            $dest . '/content/mu-plugins',
            $dest . '/content/plugins',
            $dest . '/content/themes',
            $dest . '/content/uploads',
        ];

        $directories = array_filter($directories, function ($directory) {
            return !is_dir($directory);
        });

        foreach ($directories as $directory) {
            mkdir($directory);
        }
    }

    /**
     * Copy necessary files.
     *
     * @param string $source
     * @param string $dest
     *
     * @return void
     */
    private function copyFiles(string $source, string $dest): void
    {
        $files = [
            $source . '/index.php.stub'     => $dest . '/index.php',
            $source . '/wp-config.php.stub' => $dest . '/wp-config.php'
        ];

        $files = array_filter($files, function ($file) {
            return !file_exists($file);
        });

        foreach ($files as $source => $dest) {
            copy($source, $dest);
        }
    }
}
