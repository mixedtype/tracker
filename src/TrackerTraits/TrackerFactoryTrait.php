<?php

namespace Mixedtype\Tracker\TrackerTraits;

use Mixedtype\Tracker\Tracker;

trait TrackerFactoryTrait
{
    protected static $baseDir;
    protected static ?Tracker $instance = null;

    protected $directory;

    protected $writeIsEnabled;

    protected $trackerIsEnabled;

    protected $config;

    protected function __construct()
    {
        // protected constructor to prevent direct object creation
    }

    protected function __clone()
    {
        // prevent instance from being cloned
    }

    public static function getInstance($baseDir = null) : Tracker
    {
        if (null === self::$instance) {
            if(self::$baseDir === null) {
                self::$baseDir = rtrim($baseDir, '/') . '/';
            }

            self::$instance = new self();
            self::$instance->writeIsEnabled = false;
            self::$instance->trackerIsEnabled = self::loadConfigFile();

            if(self::$instance->trackerIsEnabled) {
                if (isset(self::$instance->config['groups']['app']['enabled'])) {
                    self::$instance->trackerIsEnabled = self::$instance->config['groups']['app']['enabled'] === true;
                }
                if (self::$instance->trackerIsEnabled && isset(self::$instance->config['groups']['app']['write'])) {
                    self::$instance->writeIsEnabled = self::$instance->config['groups']['app']['write'] === true;
                }

                if(self::$instance->writeIsEnabled) {
                    $isArtisan = false;
                    if(isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME'] === 'artisan') {
                        $isArtisan = true;
                    }

                    if (!isset(self::$instance->config['groups']['app']['file_storage_path'])) {
                        throw new \Exception('Tracker config is missing types.app.file_storage_path');
                    }
                    self::$instance->directory = 'storage/' . self::$instance->config['groups']['app']['file_storage_path']
                        . substr(date('Y-m-d-H-i'), 0, 15) . '0'
                        . ($isArtisan?'-c':'') . '/';
                }
            }
            if(self::$baseDir === null) {
                self::$instance->writeIsEnabled = false;
            }
        }

        return self::$instance;
    }

    private static function loadConfigFile()
    {
        if(!file_exists(self::$baseDir . 'config/mixedtype/tracker.php')) {
            return false;
        }
        self::$instance->config = require self::$baseDir . 'config/mixedtype/tracker.php';
        return true;
    }

    public function getBasePath()
    {
        return self::$baseDir . substr($this->directory, 0,
                strrpos(rtrim($this->directory, '/'), '/')
            ) . '/';
    }

    public function getCurrentPath()
    {
        return self::$baseDir . $this->directory;
    }
}
