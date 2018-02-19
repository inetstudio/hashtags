<?php

namespace InetStudio\Hashtags\Events\Prizes;

use Illuminate\Queue\SerializesModels;
use InetStudio\Hashtags\Contracts\Events\Prizes\ModifyPrizeEventContract;

/**
 * Class ModifyPrizeEvent.
 */
class ModifyPrizeEvent implements ModifyPrizeEventContract
{
    use SerializesModels;

    public $object;

    /**
     * Create a new event instance.
     *
     * ModifyPrizeEvent constructor.
     *
     * @param $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }
}
