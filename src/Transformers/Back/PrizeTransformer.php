<?php

namespace InetStudio\Hashtags\Transformers\Back;

use League\Fractal\TransformerAbstract;
use InetStudio\Hashtags\Models\PrizeModel;

/**
 * Class PrizeTransformer
 * @package InetStudio\Hashtags\Transformers\Back
 */
class PrizeTransformer extends TransformerAbstract
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
                'id' => $prize->id
            ])->render(),
        ];
    }
}
