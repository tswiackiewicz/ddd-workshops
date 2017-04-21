<?php

date_default_timezone_set('UTC');

// PHPStorm fix for @runTestsInSeparateProcesses
if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    define('PHPUNIT_COMPOSER_INSTALL', __DIR__ . '/../vendor/autoload.php');
}

require_once __DIR__ . '/../vendor/autoload.php';
