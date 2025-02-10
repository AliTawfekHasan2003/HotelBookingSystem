<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class Translation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'translatable_id',
        'translatable_type',
        'language',
        'attribute',
        'value',
    ];

    public function translatable()
    {
        return $this->morphTo();
    }

    public function updateTranslation($value)
    {
        $this->update([
            'value' => $value,
        ]);
    }

    public function scopeAttribute(Builder $query, $attribute)
    {
        return $query->where('attribute', $attribute);
    }

    public function scopeLanguage(Builder $query, $language)
    {
        return $query->where('language', $language);
    }
}
