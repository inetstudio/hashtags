<?php

namespace InetStudio\Hashtags\Events\Posts;

use Illuminate\Queue\SerializesModels;
use InetStudio\Hashtags\Contracts\Events\Posts\ModifyPostEventContract;

/**
 * Class ModifyPostEvent.
 */
class ModifyPostEvent implements ModifyPostEventContract
{
    use SerializesModels;

    public $object;

    /**
     * Create a new event instance.
     *
     * ModifyPostEvent constructor.
     *
     * @param $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }
}
