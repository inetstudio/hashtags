<?php

namespace InetStudio\Hashtags\Events\Points;

use Illuminate\Queue\SerializesModels;
use InetStudio\Hashtags\Contracts\Events\Points\ModifyPointEventContract;

/**
 * Class ModifyPointEvent.
 */
class ModifyPointEvent implements ModifyPointEventContract
{
    use SerializesModels;

    public $object;

    /**
     * Create a new event instance.
     *
     * ModifyPointEvent constructor.
     *
     * @param $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }
}
