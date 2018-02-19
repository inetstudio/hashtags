<?php

namespace InetStudio\Hashtags\Listeners\Back\Posts;

use Illuminate\Support\Facades\Cache;

class ClearPostsCacheListener
{
    /**
     * ClearPostsCacheListener constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param $event
     */
    public function handle($event): void
    {
        $object = $event->object;

        $cacheKey = 'posts_'.md5($object->id.'_'.$object->status_id);

        Cache::forget($cacheKey);
    }
}
