<?php

namespace InetStudio\Hashtags\Models;

use Rutorika\Sortable\SortableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

/**
 * Модель поста.
 *
 * Class ContestPost
 */
class PostModel extends Model
{
    use SoftDeletes;
    use SortableTrait;
    use SearchableTrait;

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'hashtags_posts';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'hash', 'social_id', 'social_type', 'status_id', 'stage_id', 'prize_id', 'author_id', 'last_editor_id',
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
     * Сортировка в пределах статуса.
     *
     * @var string
     */
    protected static $sortableGroupField = 'status_id';

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'hashtags_posts.hash' => 10,
        ],
    ];

    /**
     * Полиморфное отношение с моделями социальных постов.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function social()
    {
        return $this->morphTo();
    }

    /**
     * Отношение "один к одному" с моделью статуса.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status()
    {
        return $this->hasOne(StatusModel::class, 'id', 'status_id');
    }

    /**
     * Отношение "один к одному" с моделью этапа.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function stage()
    {
        return $this->hasOne(StageModel::class, 'id', 'stage_id');
    }

    /**
     * Отношение "один к одному" с моделью приза.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function prize()
    {
        return $this->hasOne(PrizeModel::class, 'id', 'prize_id');
    }

    /**
     * Отношение "многие ко многим" с моделью тега.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(TagModel::class, 'hashtags_posts_tags', 'post_id', 'tag_id')->withPivot('point_id')->withTimestamps();
    }

    /**
     * Отношение "многие ко многим" с моделью баллов.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function points()
    {
        return $this->belongsToMany(PointModel::class, 'hashtags_posts_points', 'post_id', 'point_id')->withPivot('tag_id')->withTimestamps();
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
