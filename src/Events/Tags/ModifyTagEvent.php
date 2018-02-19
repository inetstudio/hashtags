<?php

namespace InetStudio\Hashtags\Events\Tags;

use Illuminate\Queue\SerializesModels;
use InetStudio\Hashtags\Contracts\Events\Tags\ModifyTagEventContract;

/**
 * Class ModifyTagEvent.
 */
class ModifyTagEvent implements ModifyTagEventContract
{
    use SerializesModels;

    public $object;

    /**
     * Create a new event instance.
     *
     * ModifyTagEvent constructor.
     *
     * @param $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }
}
