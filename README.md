# Laravel Request Field Types

![PHP 7.0+](https://img.shields.io/badge/php-7.0%2B-blue.svg)
[![Build Status](https://img.shields.io/travis/rickselby/laravel-request-field-types.svg)](https://travis-ci.org/rickselby/laravel-request-field-types)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/065c32de-1142-4943-b5ed-b5ce6771ec8a.svg)](https://insight.sensiolabs.com/projects/065c32de-1142-4943-b5ed-b5ce6771ec8a)
[![Code Coverage](https://img.shields.io/codecov/c/github/rickselby/laravel-request-field-types.svg)](https://codecov.io/gh/rickselby/laravel-request-field-types)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)

A way of defining common input field types in a central location and use them among all requests in your app.

Tested on PHP >= 7.0, Laravel >= 5.4.

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
    $this->setInputsFor('date', ['start_date', 'end_date']);
}
```

And we're done. The request will use the rules defined for the `DateField` class for those input fields.

We can define further rules if we need, or add rules to defined fields:

```php
protected function defineRules()
{
    // We can mix keyed and non-keyed field names as required
    $this->setInputsFor('date', [
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

### Other use cases (that prompted me to write this package)

*Amounts of money* - Inputs accept money as a decimal (£1.99), but the app will handle money as the smallest unit (199 pence).
There is a facade to assist with converting between the two formats, and inputs are converted in the request.

*DateIntervals* - Various periods of time for repeating things (1 month, 2 weeks, etc). Input is split into two fields,
a numeric value and a drop-down for the period. Validation needs to know there are two fields but the app will work with
a single value; we can convert the input to a single value to be passed to the rest of the app.
