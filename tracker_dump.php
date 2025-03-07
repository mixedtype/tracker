<?php

if (!function_exists('dump')) {
    /**
     * @param mixed ...$vars
     * @return mixed
     */
    function dump(...$vars): mixed
    {
        return \Mixedtype\Tracker\Tracker::dump($vars);
    }
}

if (!function_exists('dd')) {
    /**
     * @param mixed ...$vars
     * @return never
     */
    function dd(...$vars): never
    {
        \Mixedtype\Tracker\Tracker::dd($vars);
        exit(1);
    }
}
