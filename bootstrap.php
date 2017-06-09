<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    FuelPHP - Postal
 * @version    0.1 develop
 * @author     Synergi Tech Ltd
 * @license    MIT License
 * @copyright  2017 Synergi Tech Ltd
 * @link       http://github.com/SynergiTech
 */

\Autoloader::add_core_namespace('Synergitech');

\Autoloader::add_classes(array(
    'Synergitech\\Postal' => __DIR__.'/classes/postal.php',
    'Synergitech\\Postal\\Webhook' => __DIR__.'/classes/webhook.php',
    'Synergitech\\Postal\\Email' => __DIR__.'/classes/model/email.php',
    'Synergitech\\Postal\\Email\\Webhook' => __DIR__.'/classes/model/email/webhook.php'
));
