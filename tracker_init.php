<?php

define('MIXEDTYPE_TRACKER_INIT', microtime(true));

if(!defined('REQUEST_ID')) {
    define('REQUEST_ID', uniqid('app', true));
}
if(!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}


$includedFiles = get_included_files();
$dirs = explode('/public/index.php', $includedFiles[0]);
if(count($dirs) > 1) {
    $baseDir = $dirs[0];
} else {
    $dirs = explode('/artisan', $includedFiles[0]);
    if(count($dirs) > 1) {
        $baseDir = $dirs[0];
    } else {
        $baseDir = null;
    }
}

require_once 'tracker_dump.php';
require_once 'src/TrackerTraits/TrackerFactoryTrait.php';
require_once 'src/TrackerTraits/TrackerAppTrait.php';
require_once 'src/TrackerTraits/TrackerTrackTrait.php';
require_once 'src/TrackerTraits/TrackerWriterTrait.php';
require_once 'src/TrackerTraits/TrackerDebugTrait.php';
require_once 'src/Tracker.php';

\Mixedtype\Tracker\Tracker::getInstance($baseDir)
    ->trackAppBegin(true)
    ->saveApp();
