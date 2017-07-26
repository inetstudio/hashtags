<?php

namespace Inetstudio\Hashtags\Transformers;

use League\Fractal\TransformerAbstract;
use InetStudio\Hashtags\Models\StageModel;

class StageTransformer extends TransformerAbstract
{
    /**
     * @param StageModel $stage
     * @return array
     */
    public function transform(StageModel $stage)
    {
        return [
            'id' => (int) $stage->id,
            'name' => $stage->name,
            'alias' => $stage->alias,
            'created_at' => (string) $stage->created_at,
            'updated_at' => (string) $stage->updated_at,
            'actions' => view('admin.module.hashtags::pages.stages.datatables.actions', ['id' => $stage->id])->render(),
        ];
    }
}
