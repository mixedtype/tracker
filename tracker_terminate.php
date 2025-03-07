<?php
if(defined('MIXEDTYPE_TRACKER_INIT')) {
    define('TRACKER_TERMINATE', microtime(true));
    \Mixedtype\Tracker\Tracker::getInstance()->trackAppTerminateAndSave($request ?? null, $response ?? null);
}
