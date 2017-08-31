<?php

namespace Inetstudio\Hashtags\Transformers;

use League\Fractal\TransformerAbstract;
use InetStudio\Hashtags\Models\PointModel;

class PointTransformer extends TransformerAbstract
{
    /**
     * @param PointModel $point
     * @return array
     */
    public function transform(PointModel $point)
    {
        return [
            'id' => (int) $point->id,
            'name' => ((! $point->show) ? '<i class="fa fa-eye-slash"></i> ' : '').$point->name,
            'alias' => $point->alias,
            'numeric' => (int) $point->numeric,
            'created_at' => (string) $point->created_at,
            'updated_at' => (string) $point->updated_at,
            'actions' => view('admin.module.hashtags::partials.datatables.points.actions', ['id' => $point->id])->render(),
        ];
    }
}
