<?php

namespace InetStudio\Hashtags\Listeners;

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
        //Cache::tags(['hashtags_posts'])->flush();
        Cache::forget('PostsController_getGallery');
        Cache::forget('PostsController_getDaysWinners');
        Cache::forget('PostsController_getStagesWinners');
    }
}
