<?php
define('LIBRARY_PATH', dirname(__DIR__).'/library');

require_once(LIBRARY_PATH . '/Installation/Dependency.php');
require_once(LIBRARY_PATH . '/Installation/Dependency/Git.php');

$pomUpgrade = new Installation_Dependency_Git(
    'git://github.com/bonndan/POM.git',
    LIBRARY_PATH,
    'POM*'
);
$pomUpgrade->install();