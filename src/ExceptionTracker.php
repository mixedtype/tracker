<?php

namespace Mixedtype\Tracker;

class ExceptionTracker
{
    static function captureException(\Throwable $e)
    {
        if($e instanceof \Illuminate\Database\QueryException) {
            $sql = $e->getSql();
            $bindings = $e->getBindings();
            $message = $e->getMessage();
            $file = $e->getFile();
            $line = $e->getLine();
            $trace = $e->getTrace();
            $sql = json_encode($sql);
            $bindings = json_encode($bindings);
            $trace = json_encode($trace);
            Tracker::getInstance()->track('exception', [
                'message' => $message,
                'file' => $file,
                'line' => $line,
                'trace' => $trace,
                'sql' => $sql,
                'bindings' => $bindings,
            ], 'exceptions')
                ->save();
            return;
        }


        Tracker::getInstance()->track('exception', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => json_encode($e->getTrace()),
        ], 'exceptions')
            ->save();
    }
}
