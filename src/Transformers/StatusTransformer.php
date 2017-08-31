<?php

namespace Inetstudio\Hashtags\Transformers;

use League\Fractal\TransformerAbstract;
use InetStudio\Hashtags\Models\StatusModel;

class StatusTransformer extends TransformerAbstract
{
    /**
     * @param StatusModel $status
     * @return array
     */
    public function transform(StatusModel $status)
    {
        $icons = '';
        $icons .= ($status->delete) ? '<i class="fa fa-trash-o"></i> ' : '';
        $icons .= ($status->check) ? '<i class="fa fa-question"></i> ' : '';
        $icons .= ($status->default) ? '<i class="fa fa-check-square-o"></i> ' : '';
        $icons .= ($status->block) ? '<i class="fa fa-minus-circle"></i> ' : '';
        $icons .= ($status->main) ? '<i class="fa fa-thumbs-o-up"></i> ' : '';

        return [
            'id' => (int) $status->id,
            'name' => $icons.$status->name,
            'alias' => $status->alias,
            'created_at' => (string) $status->created_at,
            'updated_at' => (string) $status->updated_at,
            'actions' => view('admin.module.hashtags::partials.datatables.statuses.actions', ['id' => $status->id])->render(),
        ];
    }
}
