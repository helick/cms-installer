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
        $dest   = dirname(__DIR__, 5) . '/web';

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
        mkdir($dest);
        mkdir($dest . '/content');
        mkdir($dest . '/content/mu-plugins');
        mkdir($dest . '/content/plugins');
        mkdir($dest . '/content/themes');
        mkdir($dest . '/content/uploads');
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
        copy($source . '/index.php.stub', $dest . '/index.php');
        copy($source . '/wp-config.php.stub', $dest . '/wp-config.php');
    }
}
