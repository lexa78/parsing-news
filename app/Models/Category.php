<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Category
 * @package App\Models
 */
class Category extends Model
{
    use HasFactory;

    /**
     * Атрибуты, для которых НЕ разрешено массовое присвоение значений.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Получить все новости одной категории
     * @return HasMany
     */
    public function news(): HasMany
    {
        return $this->hasMany(NewsData::class);
    }

    /**
     * @return array
     */
    public static function getCategoriesAsArrayWithIdAsKey(): array
    {
        $result = [];
        foreach (self::all() as $category) {
            $result[$category->id] = $category->name;
        }

        return $result;
    }
}
