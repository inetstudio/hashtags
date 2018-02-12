<?php

namespace InetStudio\Hashtags\Transformers\Back;

use League\Fractal\TransformerAbstract;
use InetStudio\Hashtags\Models\PointModel;

/**
 * Class PointTransformer
 * @package InetStudio\Hashtags\Transformers\Back
 */
class PointTransformer extends TransformerAbstract
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
            'name' => ((! $point->show) ? '<i class="fa fa-eye-slash"></i> ' : '').$point->name,
            'alias' => $point->alias,
            'numeric' => (int) $point->numeric,
            'created_at' => (string) $point->created_at,
            'updated_at' => (string) $point->updated_at,
            'actions' => view('admin.module.hashtags::back.partials.datatables.points.actions', [
                'id' => $point->id
            ])->render(),
        ];
    }
}
