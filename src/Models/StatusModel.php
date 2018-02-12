<?php

namespace InetStudio\Hashtags\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;
use InetStudio\Classifiers\Models\Traits\HasClassifiers;

class StatusModel extends Model
{
    use SoftDeletes;
    use HasClassifiers;
    use RevisionableTrait;

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'hashtags_statuses';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'alias', 'description', 'default', 'main', 'check', 'delete', 'block',
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
     * Отношение "один ко многим" с моделью поста.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(PostModel::class, 'status_id', 'id');
    }
}
