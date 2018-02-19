<?php

namespace InetStudio\Hashtags\Transformers\Back\Points;

use League\Fractal\TransformerAbstract;
use InetStudio\Hashtags\Models\PointModel;
use InetStudio\Hashtags\Contracts\Transformers\Back\Points\PointTransformerContract;

/**
 * Class PointTransformer.
 */
class PointTransformer extends TransformerAbstract implements PointTransformerContract
{
    /**
     * Подготовка данных для отображения в таблице.
     *
     * @param PointModel $point
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(PointModel $point): array
    {
        return [
            'id' => (int) $point->id,
            'name' => view('admin.module.hashtags::back.partials.datatables.points.name', [
                'point' => $point,
            ])->render(),
            'alias' => $point->alias,
            'numeric' => (int) $point->numeric,
            'created_at' => (string) $point->created_at,
            'updated_at' => (string) $point->updated_at,
            'actions' => view('admin.module.hashtags::back.partials.datatables.points.actions', [
                'id' => $point->id,
            ])->render(),
        ];
    }
}
