<?php
define('LIBRARY_PATH', dirname(__DIR__).'/library');

require_once(LIBRARY_PATH . '/Installation/Dependency.php');
require_once(LIBRARY_PATH . '/Installation/Dependency/TarGz.php');

$idsInstall = new Installation_Dependency_TarGz(
    'http://phpids.org/files/phpids-0.6.5.tar.gz',
    LIBRARY_PATH,
    'lib/IDS'
);
$idsInstall->install();