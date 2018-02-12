<?php

namespace InetStudio\Hashtags\Transformers\Back;

use League\Fractal\TransformerAbstract;
use InetStudio\Hashtags\Models\TagModel;

/**
 * Class TagTransformer
 * @package InetStudio\Hashtags\Transformers\Back
 */
class TagTransformer extends TransformerAbstract
{
    /**
     * Подготовка данных для отображения в таблице.
     *
     * @param TagModel $tag
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(TagModel $tag): array
    {
        return [
            'id' => (int) $tag->id,
            'name' => $tag->name,
            'created_at' => (string) $tag->created_at,
            'updated_at' => (string) $tag->updated_at,
            'actions' => view('admin.module.hashtags::back.partials.datatables.tags.actions', [
                'id' => $tag->id
            ])->render(),
        ];
    }
}
