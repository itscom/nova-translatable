<?php

namespace Its\Nova\Translatable;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Field;

class Translatable extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'translatable';

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  mixed|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $locales = array_map(function ($value) {
            return __($value);
        }, config('translatable.locales'));

        $this->withMeta([
            'locales' => $locales,
            'indexLocale' => app()->getLocale()
        ]);
    }

    /**
     * Resolve the given attribute from the given resource.
     *
     * @param  mixed  $resource
     * @param  string  $attribute
     * @return mixed
     */
    protected function resolveAttribute($resource, $attribute)
    {
        $results = [];
        if ( class_exists('\Spatie\Translatable\TranslatableServiceProvider') ) {
            $results = $resource->getTranslations($attribute);
        } elseif ( class_exists('\Dimsav\Translatable\TranslatableServiceProvider') ) {
            $translations = $resource->translations()
                ->get(['locale', $attribute])
                ->toArray();
            foreach ( $translations as $translation ) {
                $results[$translation['locale']] = $translation[$attribute];
            }
        }
        return $results;
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if ( class_exists('\Spatie\Translatable\TranslatableServiceProvider') ) {
            parent::fillAttributeFromRequest($request, $requestAttribute, $model, $attribute);
        } elseif ( class_exists('\Dimsav\Translatable\TranslatableServiceProvider') ) {
            if ( is_array($request[$requestAttribute]) ) {
                foreach ( $request[$requestAttribute] as $lang => $value ) {
                    $model->translateOrNew($lang)->{$attribute} = $value;
                }
	    }
        }
    }

    /**
     * Set the locales to display / edit.
     *
     * @param  array  $locales
     * @return $this
     */
    public function locales(array $locales)
    {
        return $this->withMeta(['locales' => $locales]);
    }

    /**
     * Set the locale to display on index.
     *
     * @param  string $locale
     * @return $this
     */
    public function indexLocale($locale)
    {
        return $this->withMeta(['indexLocale' => $locale]);
    }

    /**
     * Set the input field to a single line text field.
     */
    public function singleLine()
    {
        return $this->withMeta(['singleLine' => true]);
    }

    /**
     * Use Trix Editor.
     */
    public function trix()
    {
        return $this->withMeta(['trix' => true]);
    }
}
