<?php

namespace InetStudio\Hashtags\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модель тега.
 *
 * Class ContestStatus
 */
class TagModel extends Model
{
    use SoftDeletes;

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'hashtags_tags';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'name',
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
     * Отношение "многие ко многим" с моделью поста.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(PostModel::class, 'hashtags_posts_tags', 'tag_id', 'post_id')->withPivot('point_id')->withTimestamps();
    }

    /**
     * Отношение "многие ко многим" с моделью баллов.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function points()
    {
        return $this->belongsToMany(PointModel::class, 'hashtags_tags_points', 'tag_id', 'point_id')->withPivot('post_id')->withTimestamps();
    }

    /**
     * Обратное отношение "один ко многим" с моделью пользователя.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(\App\User::class, 'author_id');
    }

    /**
     * Обратное отношение "один ко многим" с моделью пользователя.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function editor()
    {
        return $this->belongsTo(\App\User::class, 'last_editor_id');
    }
}
