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

            if(isset($this->config['groups']['app']['ignore_backtrace_paths'])) {
                foreach ($this->config['groups']['app']['ignore_backtrace_paths'] as $path) {
                    if (strpos($item['file'], $path) !== false) {
                        continue 2;
                    }
                }
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

    static function dd(...$vars)
    {
        if (!in_array(\PHP_SAPI, ['cli', 'phpdbg'], true) && !headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
        }

        $tracker = self::getInstance();
        $tracker->track('dd', array_merge(['data' => $vars], $tracker->calledFrom()), 'debug');
        $tracker->save();


        foreach ($vars as $v) {
            VarDumper::dump(array_merge(['data' => $vars], $tracker->calledFrom()));
        }

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
            $tracker->track('dump', array_merge(['data' => $vars[0]], $tracker->calledFrom()), 'debug');
            VarDumper::dump($vars[0]);
            $k = 0;
        } else {
            foreach ($vars as $k => $v) {
                $tracker->track('dump', array_merge(['data' => $v], $tracker->calledFrom()), 'debug');
                VarDumper::dump($v, is_int($k) ? 1 + $k : $k);
            }
        }

        if (1 < count($vars)) {
            return $vars;
        }

        return $vars[$k];
    }
}
