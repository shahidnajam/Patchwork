<?php
define('LIBRARY_PATH', dirname(__DIR__).'/library');

require_once(LIBRARY_PATH . '/Installation/Dependency.php');
require_once(LIBRARY_PATH . '/Installation/Dependency/Git.php');

$zfd = new Installation_Dependency_Git(
    'git://github.com/beberlei/zf-doctrine.git', 
    LIBRARY_PATH,
    'library/'
);
$zfd->install();