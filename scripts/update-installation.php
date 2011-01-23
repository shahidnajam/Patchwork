<?php
$dir = realpath(__DIR__ . '/../library/');

require_once $dir . '/Installation/Dependency.php';
require_once $dir . '/Installation/Dependency/Git.php';
require_once $dir . '/Installation/Dependency/SelfUpdate.php';
$installationUpdate = new Installation_Dependency_SelfUpdate();
$installationUpdate->install();