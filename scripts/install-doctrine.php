<?php
define('LIBRARY_PATH', dirname(__DIR__).'/library');

require_once(LIBRARY_PATH . '/Installation/Dependency.php');
require_once(LIBRARY_PATH . '/Installation/Dependency/Git.php');

$doctrine = new Installation_Dependency_Git(
    'git://github.com/doctrine/doctrine1.git',
    LIBRARY_PATH,
    'lib/Doctrine*'
);
$doctrine->install();