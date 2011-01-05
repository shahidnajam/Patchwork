<?php
$doctrine = new Installation_Dependency_Git(
    'git://github.com/doctrine/doctrine1.git',
    dirname(__DIR__).'/library',
    'Doctrine*'
);
$doctrine->install();