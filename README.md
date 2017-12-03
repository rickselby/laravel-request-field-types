# Laravel Request Field Types

![PHP 7.0+](https://img.shields.io/badge/php-7.0%2B-blue.svg)
[![Build Status](https://img.shields.io/travis/rickselby/laravel-request-field-types.svg)](https://travis-ci.org/rickselby/laravel-request-field-types)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/065c32de-1142-4943-b5ed-b5ce6771ec8a.svg)](https://insight.sensiolabs.com/projects/065c32de-1142-4943-b5ed-b5ce6771ec8a)
[![Code Coverage](https://img.shields.io/codecov/c/github/rickselby/laravel-request-field-types.svg)](https://codecov.io/gh/rickselby/laravel-request-field-types)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)

A way of defining common input field types in a central location and use them among all requests in your app.

Tested on PHP >= 7.0, Laravel >= 5.4.

## Installing

```php
$ composer require rickselby/laravel-request-field-types
```

Under Laravel 5.5, the package will be automatically discovered.

## Terminology

'Field' is used as two different terms here; I hope I've been clear throughout the documentation:

* **Field Type** - e.g. date, name, email...
* **Input Field** - the fields posted as part of a request

## Defining field types

Each field must implement `FieldTypeInterface`. `BaseFieldType` implements the interface and sets up
common functions, and is a good starting place to implementing your own fields.

Three things need implementing from the `BaseFieldType`:

* `const ID` - a unique identifier for the field
* `rules()` - the default rules for an input field
* `setMessagesFor($inputField)` - define any custom messages for the input field

An example field is included (DateFieldType).

### Registering field types

Each field type needs to be registered; this can be done in a service provider:

```php
FieldTypes::register(RickSelby\LaravelRequestFieldTypes\Fields\DateFieldType::class);
```

## Using field types in requests

Start by extending `RickSelby\LaravelRequestFieldTypes\FieldTypesRequest`
instead of `Illuminate\Foundation\Http\FormRequest`.

Then, two functions need defining:

* **defineRules()**
* **defineMessages()**

There is no need to define `rules()` and `messages()`; these are managed within the class.

### `defineRules()`

Instead of adding rules to an array in `rules()`, we can define them using functions here.

For a defined field types, use `setInputsFor()`:

```php
$this->setInputsFor(DateFieldType::ID, ['start_date', 'end_date']);

// Passing a key => value pair allows extra rules to be added to an input field;
$this->setInputsFor(DateFieldType::ID, ['start_date' => 'required']);

// Keyed and non-keyed field names can be mixed as required
$this->setInputsFor(Date::ID, ['start_date' => 'required', 'end_date']);
```

For other fields, rules can be set directly with `setRules()`:
```php
$this->setRules('otherfield', ['required', 'numeric']);
```

#### Ordering

The request keeps track of the order rules are set, and returns the rules in the given order, so the validation
messages are returned in the desired order. It is possible to override the field order, if preferred:

```php
$this->setFieldOrder(['field1', 'field2'...]);
```

### `defineMessages()`

Custom messages for defined field types' default rules can be set in the field type.
Other messages can be set for rules using `setMessage()`

```php
$this->setMessage('start_date.required', 'A start date must be provided.');
```

## Modifying the request data

_(This is probably a contentious way of modifying input, but it makes sense to me...)_

Say we have a date field. The input field knows what format will be generated, and the request will
know what format to validate. Where does it get converted for use in the rest of the app? Do we
need to define the date format elsewhere as well, or can this be handled in the request?

Since the request knows about the expected input formats, it seems the right place to modify (valid) data
for use in the rest of the app.

The supplied `DateFieldType` does this; the `mapAfterValidationFunction` will be run on all input fields set for this
field type once validation has suceeded but before the validation returns.

If you need to do more complex alterations to the request data, the `modifyInputAfterValidation` function can be
overridden directly.

## ...but why?

This was mostly driven by the desire to modify the request data.

Starting with a date field - must I define the date format in every request? Can I have a single base
request that others extend from, and I define it there? Can I convert the input to a carbon instance
before it is returned to the app?

Then, other fields fell into the same pattern - other inputs that could be modified to a better format
for use in the app. The base request for the app became large and unwieldy, and thus this class was born.

Perhaps it is overkill, even for defining common field types within an app.
