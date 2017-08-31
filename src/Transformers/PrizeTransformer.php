<?php

namespace Inetstudio\Hashtags\Transformers;

use League\Fractal\TransformerAbstract;
use InetStudio\Hashtags\Models\PrizeModel;

class PrizeTransformer extends TransformerAbstract
{
    /**
     * @param PrizeModel $prize
     * @return array
     */
    public function transform(PrizeModel $prize)
    {
        return [
            'id' => (int) $prize->id,
            'name' => $prize->name,
            'alias' => $prize->alias,
            'created_at' => (string) $prize->created_at,
            'updated_at' => (string) $prize->updated_at,
            'actions' => view('admin.module.hashtags::partials.datatables.prizes.actions', ['id' => $prize->id])->render(),
        ];
    }
}
