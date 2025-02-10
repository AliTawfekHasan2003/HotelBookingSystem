<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

trait TranslationTrait
{
    public function getAttributeTranslation($attribute)
    {
        $lang = App::getLocale();
              
        return $this->translations()->attribute($attribute)->language($lang)->pluck('value')->first();
    }

    public function translationFilter(Builder $query, $attribute, $value)
    {
        return $query->whereHas('translations', function ($q) use ($attribute, $value) {
            $q->attribute($attribute)->where('value', 'like', "%{$value}%");
        });
    }

    public function translationRule($isRequired, $language, $min)
    {

        $required = $isRequired ? ['required'] : ['nullable'];
        $rule = ['string', 'min:' . $min . ''];

        --$min;
        $rule[] = $language === 'en'
            ? 'regex:/^[\p{Latin}][\p{Latin}\d_., ]{' . $min . ',}$/u'
            : 'regex:/^[\p{Arabic}][\p{Arabic}\d_.، ]{' . $min . ',}$/u';

        return array_merge($required, $rule);
    }

    public function handelSoftDeletingTranslations($status, $obj)
    {
        if (!$obj->translations()->exists()) {
            Log::error('Translation operation failed. Object type: ' . get_class($obj) . ', ID: ' . $obj->id . '. No translations found.');
            return false;
        }
        switch ($status) {
            case 'soft':
                $obj->translations()->delete();
                break;
            case 'force':
                $obj->translations()->forceDelete();
                break;
            case 'restore':
                $obj->translations()->restore();
                break;
            default:
                Log::warning('Unhandled deleting status: ' . $status . '  Object type: ' . get_class($obj) . ' for object ID: ' . $obj->id);
                return false;
                break;
        }
        return true;
    }
}
