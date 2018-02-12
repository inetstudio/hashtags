<?php

namespace InetStudio\Hashtags\Models;

use Rutorika\Sortable\SortableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;

class PostModel extends Model
{
    use SoftDeletes;
    use SortableTrait;
    use RevisionableTrait;

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
        'hash', 'social_id', 'social_type', 'status_id', 'position',
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

    protected $revisionCreationsEnabled = true;

    /**
     * Сортировка в пределах статуса.
     *
     * @var string
     */
    protected static $sortableGroupField = 'status_id';

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
     * Отношение "один к одному" с моделью приза.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function prizes()
    {
        return $this->belongsToMany(PrizeModel::class, 'hashtags_posts_prizes', 'post_id', 'prize_id')->withPivot(['stage_id', 'date'])->withTimestamps();
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
}
