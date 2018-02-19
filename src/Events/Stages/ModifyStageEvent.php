<?php

namespace InetStudio\Hashtags\Events\Stages;

use Illuminate\Queue\SerializesModels;
use InetStudio\Hashtags\Contracts\Events\Stages\ModifyStageEventContract;

/**
 * Class ModifyStageEvent.
 */
class ModifyStageEvent implements ModifyStageEventContract
{
    use SerializesModels;

    public $object;

    /**
     * Create a new event instance.
     *
     * ModifyStageEvent constructor.
     *
     * @param $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }
}
