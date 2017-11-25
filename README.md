# Laravel Request Field Types

![PHP 7.0+](https://img.shields.io/badge/php-7.0%2B-blue.svg)
[![Build Status](https://img.shields.io/travis/rickselby/laravel-request-field-types.svg)](https://travis-ci.org/rickselby/laravel-auto-presenter-mapper)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/065c32de-1142-4943-b5ed-b5ce6771ec8a.svg)](https://insight.sensiolabs.com/projects/6a69b118-1651-418b-a8b5-f2780dbc893c)
[![Code Coverage](https://img.shields.io/codecov/c/github/rickselby/laravel-request-field-types.svg)](https://codecov.io/gh/rickselby/laravel-auto-presenter-mapper)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)

A way of defining common input field types in a central location and use them among all requests in your app.

## Example usage

Our app has a common date field input across many different pages. Ordinarily, we'd take our expected format string, and have it plastered across all our request rules:
  
```php
date_format:"Y-m-d"
```
  
Using this package, we can define this format in a single location, allowing for easier updating should our front end change.

Let's include the package:

    $ composer require rickselby/laravel-request-field-types
    
Under Laravel 5.5, it will be automatically discovered.

Next, we need to register the fields we will be using in the app. We can do this in the `boot()` function `app/Providers/AppServiceProvider.php` (or any loaded ServicePrivoder, if you prefer to create your own):

```php
FieldTypes::register(RickSelby\LaravelRequestFieldTypes\Fields\DateFieldType::class);
```
    
Now to our request. Start by extending `RickSelby\LaravelRequestFieldTypes\FieldTypesRequest` instead of `Illuminate\Foundation\Http\FormRequest`:

```php
use RickSelby\LaravelRequestFieldTypes\FieldTypesRequest;

class ExampleRequest extends FieldTypesRequest
{

}
```

Now we need somewhere to define the rules for this request. `defineRules()` is set up to be called before validation. (The `FieldTypesRequest` defines the `rules()` function to pulls in all defined rules and format them correctly.)

```php
protected function defineRules()
{
    $this->fields->setInputsFor('date', ['start_date', 'end_date']);
}
```

And we're done. The request will use the rules defined for the `DateField` class for those input fields.

We can define further rules if we need, or add rules to defined fields:

```php
protected function defineRules()
{
    // We can mix keyed and non-keyed field names as required
    $this->fields->setInputsFor('date', [
        'start_date' => 'required',
        'end_date' => 'nullable',
        'other_date'
    ]);
    
    // And define rules on other fields
    $this->setRules('otherfield', ['required']);
}
```

## Modifying the request data

What if our date format is something else - something that Eloquent won't accept as a date field? We need to convert the
date to a suitable format before saving it. And where better to do this, than the same place where the date format is
defined?
_(This is probably a contentious way of modifying input, but it makes sense to me!)_

The supplied `DateFieldType` does this; the `mapAfterValidationFunction` will be run on all input fields set for this
field type once validation has suceeded but before the validation returns.

If you need to do more complex alterations to the request data, the `modifyInputAfterValidation` function can be
overridden directly.
