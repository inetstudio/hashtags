<?php

namespace InetStudio\Hashtags\Events\Statuses;

use Illuminate\Queue\SerializesModels;
use InetStudio\Hashtags\Contracts\Events\Statuses\ModifyStatusEventContract;

/**
 * Class ModifyStatusEvent.
 */
class ModifyStatusEvent implements ModifyStatusEventContract
{
    use SerializesModels;

    public $object;

    /**
     * Create a new event instance.
     *
     * ModifyStatusEvent constructor.
     *
     * @param $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }
}
