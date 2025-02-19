<?php
namespace Mixedtype\Tracker;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

class TrackerServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {

    }

    public function register()
    {
//        $this->app['events']->listen('*', function ($event, $payload) {
//            Tracker::getInstance()->track('event', [
//                'eventName' => $event,
//            ]);
//        });
        $this->app['events']->listen('Illuminate\Routing\Events\RouteMatched', function (\Illuminate\Routing\Events\RouteMatched $route) {
            Tracker::getInstance()->track('route', $route->route->getName());
        });

        DB::listen(function(QueryExecuted $query) {
            Tracker::getInstance()->trackDb($query);
        });
    }
}
