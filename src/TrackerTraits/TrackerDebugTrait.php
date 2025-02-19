<?php

namespace Mixedtype\Tracker\TrackerTraits;

use Symfony\Component\VarDumper\Caster\ScalarStub;
use Symfony\Component\VarDumper\VarDumper;

trait TrackerDebugTrait
{
    public function calledFrom()
    {
        $call = null;
        $stack = debug_backtrace();
        foreach($stack as $item) {
            if(strpos($item['file'], '/vendor/laravel/framework') !== false) {
                continue;
            }
            if(strpos($item['file'], '/items/libs/Items/') !== false) {
                continue;
            }
            if(strpos($item['file'], 'src/TrackerServiceProvider.php') !== false) {
                continue;
            }
            if(strpos($item['file'], 'src/Tracker.php') !== false) {
                continue;
            }
            if(strpos($item['file'], 'TrackerTraits/TrackerDebugTrait.php') !== false) {
                continue;
            }
            if(strpos($item['file'], 'tracker/tracker_dump.php') !== false) {
                continue;
            }

            if(strpos($item['file'], 'truEngine/.lib/_db.module.php') !== false) {
                continue;
            }
            if(strpos($item['file'], 'truEngine/classes/db/db.php') !== false) {
                continue;
            }


            $call = $item;
            break;
        }
        if(substr($item['file'], 0, strlen(base_path())) === base_path()) {
            $call['file'] = substr($item['file'], strlen(base_path()) + 1);
        }

        return [
            'file' => $call['file'] ?? null,
            'line' => $call['line'] ?? null,
        ];
    }


    // todo: refactor to use VarDumper::dump - registrujem si vlastny handler

    static function dd($data)
    {
        $tracker = self::getInstance();
        $tracker->track('dd', ['data' => $data, ...$tracker->calledFrom()], 'debug');
        $tracker->save();


        echo '<pre>';
        print_r(['data' => $data, ...$tracker->calledFrom()]);
        echo '</pre>';
        die();
    }

    static function dump(mixed ...$vars): mixed
    {
        $tracker = self::getInstance();

        if (!$vars) {
            $tracker->track('dump', ['data' => new ScalarStub('🐛'), ...$tracker->calledFrom()], 'debug');
            VarDumper::dump(new ScalarStub('🐛'));

            return null;
        }

        if (array_key_exists(0, $vars) && 1 === count($vars)) {
            $tracker->track('dump', ['data' => $vars[0], ...$tracker->calledFrom()], 'debug');
            VarDumper::dump($vars[0]);
            $k = 0;
        } else {
            foreach ($vars as $k => $v) {
                $tracker->track('dump', ['data' => $v, ...$tracker->calledFrom()], 'debug');
                VarDumper::dump($v, is_int($k) ? 1 + $k : $k);
            }
        }

        if (1 < count($vars)) {
            return $vars;
        }

        return $vars[$k];
    }
}
