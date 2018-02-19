<?php

namespace InetStudio\Hashtags\Transformers\Back\Prizes;

use League\Fractal\TransformerAbstract;
use InetStudio\Hashtags\Models\PrizeModel;
use InetStudio\Hashtags\Contracts\Transformers\Back\Prizes\PrizeTransformerContract;

/**
 * Class PrizeTransformer.
 */
class PrizeTransformer extends TransformerAbstract implements PrizeTransformerContract
{
    /**
     * Подготовка данных для отображения в таблице.
     *
     * @param PrizeModel $prize
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(PrizeModel $prize): array
    {
        return [
            'id' => (int) $prize->id,
            'name' => $prize->name,
            'alias' => $prize->alias,
            'created_at' => (string) $prize->created_at,
            'updated_at' => (string) $prize->updated_at,
            'actions' => view('admin.module.hashtags::back.partials.datatables.prizes.actions', [
                'id' => $prize->id,
            ])->render(),
        ];
    }
}
