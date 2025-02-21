<?php

namespace Mixedtype\Tracker\TrackerTraits;

use Illuminate\Http\Request;
use Mixedtype\Tracker\Tracker;

trait TrackerAppTrait
{
    protected static $appWasInitialized = false;
    protected static $appWasSaved = false;
    protected static $request_id = null;

    public static function appWasInitialized() : bool
    {
        return self::$appWasInitialized;
    }

    public static function appWasSaved() : bool
    {
        return self::$appWasSaved;
    }

    public function getRequestId() : string
    {
        if(self::$request_id) {
            return self::$request_id;
        }

        if(defined('REQUEST_ID')) {
            self::$request_id = REQUEST_ID;
            return self::$request_id;
        }

        self::$request_id = uniqid('apx', true);

        return self::$request_id;
    }

    public function trackAppBegin($earlyStart = false) : Tracker
    {
        if(!$this->trackerIsEnabled) {
            return $this;
        }

        if(!self::appWasInitialized()) {
            $data = [
                'request_id' => $this->getRequestId(),
                //'timezone' => date_default_timezone_get(),
                'app_start' => LARAVEL_START,
                'request_uri' => $_SERVER['REQUEST_URI'] ?? null,
                'request_method' => $_SERVER['REQUEST_METHOD'] ?? null,
                'script_name' => $_SERVER['SCRIPT_NAME'] ?? null,
                'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? null,
                'http_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'argv' => isset($_SERVER['argv']) ? json_encode($_SERVER['argv']) : null,
                'argc' => isset($_SERVER['argc']) ? $_SERVER['argc'] : null,
            ];


//        if(substr($_SERVER['REQUEST_URI'], 0, strlen('/?ajax')) === '/?ajax') {
//            $data['server'] = $_SERVER;
//        }

            if (isset($_SERVER['REQUEST_URI'])) {
                if (isset($this->config['groups']['app']['ignore_requests'])) {
                    foreach ($this->config['groups']['app']['ignore_requests'] as $ignore) {
                        if ($this->str_starts_with($_SERVER['REQUEST_URI'], $ignore)) {
                            $this->writeIsEnabled = false;
                            break;
                        }
                    }
                }
            }

            $this->begin('app', $data);
            self::$appWasInitialized = true;
        }

        if(!$earlyStart) {
            $this->trackAdvancedAppInfoFromMiddleware();
        }

        return $this;
    }

    private function str_starts_with($haystack, $needle) : bool
    {
        return strpos($haystack, $needle) === 0;
    }

    function trackAdvancedAppInfoFromMiddleware()
    {
        // todo: zalogovat routu... ale to mozem az po preroutovani.
    }

    function saveApp()
    {
        if(!$this->trackerIsEnabled) {
            return $this;
        }

        if(!self::appWasInitialized()) {
            return $this;
        }

        if(self::appWasSaved()) {
            return $this;
        }

        $this->save();
        self::$appWasSaved = true;
        return $this;
    }

    public function trackAppTerminateAndSave() : Tracker
    {
        if(!$this->trackerIsEnabled) {
            return $this;
        }

        $timestamp = microtime(true);
        $this->end('app', [
            'request_id' => $this->getRequestId(),
            'app_end' => $timestamp,
            'app_duration' => $timestamp - LARAVEL_START,
            'tracker_terminate' => defined('TRACKER_TERMINATE') ? TRACKER_TERMINATE : null,
        ]);
        $this->save();
        $this->writeIsEnabled = false;
        return $this;
    }

    public function trackAppValueAdd($tag, $addValue = 0)
    {
        if($this->fileStorageData['app'][0]['tag'] === 'app') {
            if(!isset($this->fileStorageData['app'][0]['data'][$tag])) {
                $this->fileStorageData['app'][0]['data'][$tag] = 0;
            }
            $this->fileStorageData['app'][0]['data'][$tag] += $addValue;
        }
    }



}
