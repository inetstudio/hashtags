<?php

namespace InetStudio\Hashtags\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модель баллов.
 *
 * Class ContestStatus
 */
class PointModel extends Model
{
    use SoftDeletes;

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'hashtags_points';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'alias', 'numeric', 'show', 'author_id', 'last_editor_id',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Отношение "многие ко многим" с моделью тега.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(TagModel::class, 'hashtags_tags_points', 'point_id', 'tag_id')->withPivot('post_id')->withTimestamps();
    }

    /**
     * Отношение "многие ко многим" с моделью поста.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(PostModel::class, 'hashtags_posts_points', 'point_id', 'post_id')->withPivot('tag_id')->withTimestamps();
    }
}
