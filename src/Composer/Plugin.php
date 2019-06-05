<?php

namespace Helick\CMS\Composer;

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
        $source = dirname(__DIR__, 2) . '/resources/stubs';
        $dest   = dirname($source, 3) . '/web';

        copy($source . '/index.php.stub', $dest . '/index.php');
        copy($source . '/wp-config.php.stub', $dest . '/wp-config.php');
    }
}
