<?php
declare(strict_types=1);

use PhpMyAdmin\Config;

if (!defined('PHPMYADMIN')) {
    define('PHPMYADMIN', true);
}

require_once '/var/www/html/vendor/autoload.php';
require_once '/var/www/html/libraries/functions.inc.php';

$cfg['Server'] = $cfg['Servers'] = [
    1 => [
        'host' => getenv('PMA_HOST') ?: 'db',
        'port' => (int) (getenv('PMA_PORT') ?: 3306),
        'socket' => '',
        'auth_type' => 'cookie',
        'user' => getenv('PMA_USER') ?: 'root',
        'password' => getenv('PMA_PASSWORD') ?: '',
        'AllowNoPassword' => true,
        'AllowRoot' => true,
        'ssl' => false,
        'DisableIS' => false,
    ],
];

$cfg['blowfish_secret'] = getenv('PMA_BLOWFISH_SECRET') ?: 'poliklinik-local-dev-secret';
$cfg['AllowRoot'] = true;
$cfg['TempDir'] = '/var/lib/phpmyadmin';
$cfg['SaveDir'] = '/var/lib/phpmyadmin';
$cfg['Lang'] = 'id';
$cfg['DefaultLang'] = 'id';
$cfg['ServerDefault'] = 1;
$cfg['VersionCheck'] = false;
$cfg['MaxSqlLength'] = 500000;
$cfg['config'] = '/var/lib/phpmyadmin/config.inc.php';
$cfg['SDocRoot'] = '';
