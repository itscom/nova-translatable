# Nova Translatable Field
[![Latest Version on Packagist](https://img.shields.io/packagist/v/yeswedev/nova-translatable.svg?style=flat-square)](https://packagist.org/packages/yeswedev/nova-translatable)

This package is based on [mrmonat/nova-translatable](https://github.com/mrmonat/nova-translatable).

Adds the ability to show and edit translated fields created with [dimsav/laravel-translatable](https://github.com/dimsav/laravel-translatable) package.

It will show up in the detail view like this:

<img width="400" src="https://mrmonat.de/github/images/nova-spatie-translatable-details.png">

And in the edit view like this:

<img width="400" src="https://mrmonat.de/github/images/nova-spatie-translatable-edit.png">

## Installation and usage
You can require this package using composer:

```
composer require yeswedev/nova-translatable
```

You can add the field follows:

``` php
use YesWeDev\Nova\Translatable\Translatable;

Translatable::make('Description'),
```

Make sure, that you have your Eloquent model setup correct:

- First, you need to add the `Dimsav\Translatable\Translatable`-trait.
- Next, you should create a public property `$translatedAttributes` which holds an array with all the names of attributes you wish to make translatable.
- Finally, you should make sure that all translatable attributes are set to the `text`-datatype in your database. If your database supports `json`-columns, use that.

Here's an example of a prepared model:

``` php
use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class NewsItem extends Model
{
    use Translatable;
    
    public $translatedAttributes = ['name'];
}
```


### Defining Locales
Locales can be defined via config file ```config/translatable.php``` by adding a ```locales``` array:

``` php
// config/translatable.php
return [
    ...
    'locales' => [
        'en' => 'English',
        'de' => 'German',
        'fr' => 'French',
    ],
];
```

Alternatively you can "override" the config locales with the ```locales(...)``` method:

``` php
Translatable::make('Description')->locales([
    'en' => 'English',
    'de' => 'German',
]),
```

### Single Line Option
By default the input field on the edit view is a textarea. If you want to change it to a single line input field you can add the ```singleLine()``` option:

``` php
Translatable::make('Description')->locales([...])->singleLine(),
```

### Trix Editor
You can use the trix editor for your translated fields by using the ```trix()``` option:

``` php
Translatable::make('Description')->trix(),
```

### Index View
By default the locale used when displaying the field on the index view is determined by ```app()->getLocale()```. To override this you can use the ```indexLocale($locale)``` option:

``` php
Translatable::make('Description')->indexLocale('de'),
```

