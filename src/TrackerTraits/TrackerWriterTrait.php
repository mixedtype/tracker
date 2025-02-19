<?php

namespace Mixedtype\Tracker\TrackerTraits;

trait TrackerWriterTrait
{
    private $fileStorageData = [];

    public function isWriteEnabled($group = 'app') : bool
    {
        if(!$this->trackerIsEnabled) {
            return false;
        }
        if(!$this->writeIsEnabled) {
            return false;
        }
        if($group === 'app') {
            return $this->writeIsEnabled;
        }
        if(!isset($this->config['groups'][$group])) {
            return true;
        }
        if(isset($this->config['groups'][$group]['write'])) {
            return $this->config['groups'][$group]['write'] === true;
        }
        return true;
    }


    public function getStorageForGroup($group)
    {
        if(in_array($group, ['app', 'exceptions'])) {
            return 'file';
        }

        if(isset($this->config['groups'][$group])) {
            if(isset($this->config['groups'][$group]['storage'])) {
                return $this->config['groups'][$group]['storage'];
            }
        }
        return 'file';
    }

    protected function writeData($data, $group)
    {
        if(!$this->isWriteEnabled($group)) {
            return;
        }

        $storage = $this->getStorageForGroup($group);

        if($storage === 'file') {
            $this->writeDataToFile($data, $group);
        } else if($storage === 'db') {
            $this->writeDataToDb($data, $group);
        }
    }

    private function writeDataToFile($data, $group = 'app')
    {
        $this->fileStorageData[$group][] = $data;
    }

    public function save()
    {
        if(!$this->isWriteEnabled()) {
            return;
        }

        $filename = $this->getCurrentPath() . LARAVEL_START . '-' . $this->getRequestId() . '.json';

        if(!file_exists(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }
        file_put_contents($filename, json_encode($this->fileStorageData, JSON_PRETTY_PRINT));
    }

}
