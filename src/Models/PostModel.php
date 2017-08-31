<?php

namespace InetStudio\Hashtags\Models;

use Rutorika\Sortable\SortableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модель поста.
 *
 * Class PostModel
 *
 * @property int $id
 * @property string $hash
 * @property int $social_id
 * @property string $social_type
 * @property int $status_id
 * @property int $stage_id
 * @property int $prize_id
 * @property int $author_id
 * @property int $last_editor_id
 * @property int $position
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\User $author
 * @property-read \App\User $editor
 * @property-read \Illuminate\Database\Eloquent\Collection|\InetStudio\Hashtags\Models\PointModel[] $points
 * @property-read \InetStudio\Hashtags\Models\PrizeModel $prize
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $social
 * @property-read \InetStudio\Hashtags\Models\StageModel $stage
 * @property-read \InetStudio\Hashtags\Models\StatusModel $status
 * @property-read \Illuminate\Database\Eloquent\Collection|\InetStudio\Hashtags\Models\TagModel[] $tags
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Hashtags\Models\PostModel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel search($search, $threshold = null, $entireText = false, $entireTextOnly = false)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel searchRestricted($search, $restriction, $threshold = null, $entireText = false, $entireTextOnly = false)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel sorted()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel whereLastEditorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel wherePrizeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel whereSocialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel whereSocialType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel whereStageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PostModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Hashtags\Models\PostModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Hashtags\Models\PostModel withoutTrashed()
 * @mixin \Eloquent
 */
class PostModel extends Model
{
    use SoftDeletes;
    use SortableTrait;

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
        'hash', 'social_id', 'social_type', 'status_id', 'stage_id', 'prize_id', 'main_winner',
        'author_id', 'last_editor_id',
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
