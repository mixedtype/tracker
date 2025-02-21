<?php
namespace Mixedtype\Tracker;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Mixedtype\Tracker\TrackerTraits\TrackerDebugTrait;
use Mixedtype\Tracker\TrackerTraits\TrackerFactoryTrait;
use Mixedtype\Tracker\TrackerTraits\TrackerAppTrait;
use Mixedtype\Tracker\TrackerTraits\TrackerTrackTrait;
use Mixedtype\Tracker\TrackerTraits\TrackerWriterTrait;

class Tracker
{
    use TrackerFactoryTrait;
    use TrackerAppTrait;
    use TrackerTrackTrait;
    use TrackerWriterTrait;
    use TrackerDebugTrait;

    /**
     * @param QueryExecuted|array $query
     * @return Tracker
     */
    public function trackDb($query) : Tracker
    {
        if(!$this->trackerIsEnabled) {
            return false;
        }

        if(is_array($query)) {
            return $this->track(
                'db_query',
                array_merge(
                    $query,
                    $this->calledFrom()
                ),
                'db');
        }

        $duration = round($query->time/1000, 9); // in seconds

        $this->trackAppValueAdd('db_queries_count', 1);
        $this->trackAppValueAdd('db_queries_duration', $duration);

        return $this->track('db_query', array_merge([
            'query' => $query->sql,
            'bindings' => $query->bindings,
            'duration' => $duration,
            'connection' => $query->connection->getName(),
        ], $this->calledFrom()), 'db');
    }


//    private function writeDataToDb($data, $profileTag)
//    {
//        $section = 'default';
//        if(isset($this->config[$profileTag]['section'])) {
//            $section = $this->config[$profileTag]['section'];
//        }
//        self::storeDataToDb($section, $data);
//    }
//
//
//    static function storeDataToDb($section, $data)
//    {
//        if($section === 'app') {
//            DB::table('tracker_raw')
//                ->insert([
//                    'request_id' => $data['request_id'],
//                    'user_id' => $data['user_id'],
//                    'tag' => 'app',
//                    'timestamp' => date('Y-m-d H:i:s.u', $data['app_start']),
//                    'timestamp_end' => isset($data['app_end'])?date('Y-m-d H:i:s.u', $data['app_end']):null,
//                    'duration' => isset($data['app_end'])?$data['app_duration']:null,
//                    'data' => json_encode($data)
//                ]);
//        }
//        if($section === 'db') {
//
//        }
//    }
}
