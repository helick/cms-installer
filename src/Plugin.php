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
            'post-install-cmd'   => ['install'],
            'post-update-cmd'    => ['install'],
            'post-autoload-dump' => ['discover'],
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
     * Discover mu-plugins.
     *
     * @return void
     */
    public function discover(): void
    {
        $rootDir = dirname(__DIR__, 4);

        require_once $rootDir . '/web/wordpress/wp-admin/includes/plugin.php';

        $autoPlugins = get_plugins('/../mu-plugins');
        $muPlugins   = get_mu_plugins();

        $plugins = array_diff_key($autoPlugins, $muPlugins);
        $plugins = array_keys($plugins);

        $manifestPath = $rootDir . '/bootstrap/cache/mu-plugins.php';
        $manifestData = '<?php return ' . var_export($plugins, true) . ';';

        file_put_contents($manifestPath, $manifestData);
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
            $destDir . '/bootstrap',
            $destDir . '/bootstrap/cache',
            $destDir . '/config',
            $destDir . '/config/environments',
            $destDir . '/web',
            $destDir . '/web/content',
            $destDir . '/web/content/mu-plugins',
            $destDir . '/web/content/plugins',
            $destDir . '/web/content/themes',
            $destDir . '/web/content/uploads',
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
     * @param string $sourceDir
     * @param string $destDir
     *
     * @return void
     */
    private function copyFiles(string $sourceDir, string $destDir): void
    {
        $files = [
            '/config/environments/development.php',
            '/config/environments/staging.php',
            '/config/application.php',
            '/web/index.php',
            '/web/wp-config.php',
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
