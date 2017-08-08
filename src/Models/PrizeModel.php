<?php

namespace InetStudio\Hashtags\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модель приза.
 *
 * Class PrizeModel
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property string|null $description
 * @property int $author_id
 * @property int $last_editor_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\User $author
 * @property-read \App\User $editor
 * @property-read \Illuminate\Database\Eloquent\Collection|\InetStudio\Hashtags\Models\PostModel[] $posts
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Hashtags\Models\PrizeModel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PrizeModel whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PrizeModel whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PrizeModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PrizeModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PrizeModel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PrizeModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PrizeModel whereLastEditorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PrizeModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Hashtags\Models\PrizeModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Hashtags\Models\PrizeModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Hashtags\Models\PrizeModel withoutTrashed()
 * @mixin \Eloquent
 */
class PrizeModel extends Model
{
    use SoftDeletes;

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'hashtags_prizes';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'alias', 'description', 'author_id', 'last_editor_id',
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
     * Отношение "один ко многим" с моделью поста.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(PostModel::class, 'prize_id', 'id');
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
