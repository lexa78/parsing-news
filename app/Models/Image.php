<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Image
 * @package App\Models
 */
class Image extends Model
{
    use HasFactory;

    /**
     * Атрибуты, для которых НЕ разрешено массовое присвоение значений.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Получить новость, к которой относится картинка
     * @return BelongsTo
     */
    public function news(): BelongsTo
    {
        return $this->belongsTo(NewsData::class, 'news_id', 'id');
    }
}
