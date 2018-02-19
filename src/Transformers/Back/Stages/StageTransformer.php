<?php

namespace InetStudio\Hashtags\Transformers\Back\Stages;

use League\Fractal\TransformerAbstract;
use InetStudio\Hashtags\Models\StageModel;
use InetStudio\Hashtags\Contracts\Transformers\Back\Stages\StageTransformerContract;

/**
 * Class StageTransformer.
 */
class StageTransformer extends TransformerAbstract implements StageTransformerContract
{
    /**
     * Подготовка данных для отображения в таблице.
     *
     * @param StageModel $stage
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(StageModel $stage): array
    {
        return [
            'id' => (int) $stage->id,
            'name' => $stage->name,
            'alias' => $stage->alias,
            'created_at' => (string) $stage->created_at,
            'updated_at' => (string) $stage->updated_at,
            'actions' => view('admin.module.hashtags::back.partials.datatables.stages.actions', [
                'id' => $stage->id,
            ])->render(),
        ];
    }
}
