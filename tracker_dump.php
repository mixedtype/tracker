<?php

if (!function_exists('dump')) {
    function dump(mixed ...$vars): mixed
    {
        return \Mixedtype\Tracker\Tracker::dump($vars);
    }
}

if (!function_exists('dd')) {
    function dd(mixed ...$vars): never
    {
        \Mixedtype\Tracker\Tracker::dd($vars);
        exit(1);
    }
}
