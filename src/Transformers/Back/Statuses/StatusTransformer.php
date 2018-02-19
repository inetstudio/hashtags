<?php

namespace InetStudio\Hashtags\Transformers\Back\Statuses;

use League\Fractal\TransformerAbstract;
use InetStudio\Hashtags\Models\StatusModel;
use InetStudio\Hashtags\Contracts\Transformers\Back\Statuses\StatusTransformerContract;

/**
 * Class StatusTransformer.
 */
class StatusTransformer extends TransformerAbstract implements StatusTransformerContract
{
    /**
     * Подготовка данных для отображения в таблице.
     *
     * @param StatusModel $status
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(StatusModel $status): array
    {
        return [
            'id' => (int) $status->id,
            'name' => view('admin.module.hashtags::back.partials.datatables.statuses.name', [
                'status' => $status,
            ])->render(),
            'alias' => $status->alias,
            'created_at' => (string) $status->created_at,
            'updated_at' => (string) $status->updated_at,
            'actions' => view('admin.module.hashtags::back.partials.datatables.statuses.actions', [
                'id' => $status->id,
            ])->render(),
        ];
    }
}
