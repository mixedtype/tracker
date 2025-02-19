<?php

namespace Mixedtype\Tracker\TrackerTraits;

use Mixedtype\Tracker\Tracker;

trait TrackerTrackTrait
{
    public function isTrackEnabled($group = 'app') : bool
    {
        if(!$this->trackerIsEnabled) {
            return false;
        }
        if($group === 'app') {
            return $this->trackerIsEnabled;
        }
        if(!isset($this->config['groups'][$group])) {
            return true;
        }
        if(isset($this->config['groups'][$group]['enabled'])) {
            return $this->config['groups'][$group]['enabled'] === true;
        }
        return true;
    }


    public function begin($tag, $data = null, $group = 'app') : Tracker
    {
        $this->trackData([
            'tag' => $tag,
            'type' => 'begin',
            'data' => $data,
            'timestamp' => microtime(true)
        ], $group);
        return $this;
    }

    public function end($tag, $data = null, $group = 'app') : Tracker
    {
        $this->trackData([
            'tag' => $tag,
            'type' => 'end',
            'data' => $data,
            'timestamp' => microtime(true)
        ], $group);

        return $this;
    }


    public function track($tag, $data = null, $group = 'app') : Tracker
    {
        $this->trackData([
            'tag' => $tag,
            'type' => 'track',
            'data' => $data,
            'timestamp' => microtime(true)
        ], $group);
        return $this;
    }

    protected function trackData($data, $group) : void
    {
        if(!$this->isTrackEnabled($group)) {
            return;
        }
        $this->tracked[$group][] = $data;

        $this->writeData($data, $group);
    }

}
