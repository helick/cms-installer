<?php
/*
Plugin Name:    Helick Autoloader
Description:    The Helick autoloader enables standard plugins to be loaded as must-use ones.
Author:         Evgenii Nasyrov
Author URI:     https://helick.io/
*/

use const Helick\CMS\ROOT_DIR;

/**
 * Load the required must-use plugins.
 */
$requiredPlugins = require ROOT_DIR . '/bootstrap/cache/required-mu-plugins.php';

array_walk($requiredPlugins, static function (string $plugin) {
    require __DIR__ . DIRECTORY_SEPARATOR . $plugin;
});

unset($requiredPlugins);

/**
 * Adjust admin view to display must-use plugins.
 */
add_filter('show_advanced_plugins', function (bool $show, string $type) {
    $screen = get_current_screen();

    $isRequiredScreen = in_array($screen->base, ['plugins-network', 'plugins'], true);
    $isRequiredType   = $type === 'mustuse';
    $isUserAllowed    = current_user_can('activate_plugins');

    if (!$isRequiredScreen || !$isRequiredType || !$isUserAllowed) {
        return $show;
    }

    $GLOBALS['plugins']['mustuse'] = require ROOT_DIR . '/bootstrap/cache/mu-plugins.php';

    return false;
}, 0, 2);
