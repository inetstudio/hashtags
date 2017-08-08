<?php

namespace InetStudio\Hashtags\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модель баллов.
 *
 * Class PointModel
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property int $numeric
 * @property int $show
 * @property int $author_id
 * @property int $last_editor_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\InetStudio\Hashtags\Models\PostModel[] $posts
 * @property-read \Illuminate\Database\Eloquent\Collection|\InetStudio\Hashtags\Models\TagModel[] $tags
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Hashtags\Models\PointModel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PointModel whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PointModel whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PointModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PointModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PointModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PointModel whereLastEditorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PointModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PointModel whereNumeric($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PointModel whereShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PointModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Hashtags\Models\PointModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Hashtags\Models\PointModel withoutTrashed()
 * @mixin \Eloquent
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
