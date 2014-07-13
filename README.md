Laravel-Shortcodes
==================

Wordpress like shortcodes for Laravel 4.2.

```php
[b class="bold"]Bold text[/b]

[tabs]
  [tab]Tab 1[/tab]
  [tab]Tab 2[/tab]
[/tabs]

[user id="1" display="name"]
```

> If you are looking for BBcodes, see: https://github.com/patrickbrouwers/Laravel-BBcodes

[![Build Status](https://travis-ci.org/patrickbrouwers/Laravel-Shortcodes.svg?branch=master)](https://travis-ci.org/patrickbrouwers/Laravel-Shortcodes)
[![Latest Stable Version](https://poser.pugx.org/brouwers/shortcodes/v/stable.png)](https://packagist.org/packages/brouwers/shortcodes) [![Total Downloads](https://poser.pugx.org/brouwers/shortcodes/downloads.png)](https://packagist.org/packages/brouwers/shortcodes)  [![License](https://poser.pugx.org/brouwers/shortcodes/license.png)](https://packagist.org/packages/brouwers/shortcodes)
[![Monthly Downloads](https://poser.pugx.org/brouwers/shortcodes/d/monthly.png)](https://packagist.org/packages/brouwers/shortcodes)
[![Daily Downloads](https://poser.pugx.org/brouwers/shortcodes/d/daily.png)](https://packagist.org/packages/brouwers/shortcodes)

#Installation

Require this package in your `composer.json` and update composer.

```php
"brouwers/shortcodes": "1.*"
```

After updating composer, add the ServiceProvider to the providers array in `app/config/app.php`

```php
'Brouwers\Shortcodes\ShortcodesServiceProvider',
```

You can use the facade for shorter code. Add this to your aliases:

```php
'Shortcode' => 'Brouwers\Shortcodes\Facades\Shortcode',
```

The class is bound to the ioC as `shortcode`

```php
$shortcode = App::make('shortcode');
```

# Usage

## View compiling

By default shortcode compiling is set to false inside the config. 

### withShortcodes()

To enable the view compiling features:

```php
return View::make('view')->withShortcodes();
```

This will enable shortcode rendering for that view only.

### Config

Enabeling the shortcodes through config `shortcodes::enabled` will enable shortcoding rendering for all views.

### Enable through class

```php
Shortcode::enable();
```

### Disable through class

```php
Shortcode::disable();
```

### Disabeling some views from shortcode compiling

With the config set to true, you can disable the compiling per view.

```php
return View::make('view')->withoutShortcodes();
```

## Default compiling

To use default compiling:

```php
Shortcode::compile($contents);
```

## Registering new shortcodes

Inside a file or service provider you can register the shortcodes. (E.g. `app/start/shortcodes.php` or `App/Services/ShortcodeServiceProvider.php`)


### Callback

Shortcodes can be registered like Laravel macro's with a callback:

```php
Shortcode::register('b', function($shortcode, $content, $compiler, $name)
{
  return '<strong class="'. $shortcode->class .'">' . $content . '</strong>';
});
  
```

### Default class

```php
class BoldShortcode {

  public function register($shortcode, $content, $compiler, $name)
  {
    return '<strong class="'. $shortcode->class .'">' . $content . '</strong>';
  }
}

Shortcode::register('b', 'BoldShortcode');

```

### Class with custom method

```php
class BoldShortcode {

  public function custom($shortcode, $content, $compiler, $name)
  {
    return '<strong class="'. $shortcode->class .'">' . $content . '</strong>';
  }
}

Shortcode::register('b', 'BoldShortcode@custom');

```

### Register helpers

If you only want to show the html attribute when the attribute is provided in the shortcode, you can use `$shortcode->get($attributeKey, $fallbackValue = null)`

```php
class BoldShortcode {

  public function register($shortcode, $content, $compiler, $name)
  {
    return '<strong '. $shortcode->get('class', 'default') .'>' . $content . '</strong>';
  }
}


```
