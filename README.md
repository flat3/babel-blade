# Babel compiler for Laravel Blade

This package allows you to safely write modern, inline JavaScript in blade templates.
Compilation only happens at Blade template caching time, so has no effect on production performance.

## Installation

To install the package via composer:

`composer require flat3/babel-blade`

The compiler expects a nodejs install on the same PATH that is being used by your PHP interpreter.

You must install babel (and normally the env preset) in your Laravel project:

`npm install --dev @babel/core @babel/preset-env`

The compiler looks for a babel configuration starting from the view root (normally resources/views) and searching upwards.
You can therefore use any existing babel configuration file in your project, or you can create one in resources/views that will only be used for babel-blade.

For example at `resources/views/.babelrc.json`

```
{
  "presets": [
    [
      "@babel/preset-env",
      {
        "targets": {
          "chrome": "58",
          "ie": "11"
        }
      }
    ]
  ]
}
```

### Usage

Babel-blade looks for script blocks that use the string literal `<script type="text/babel">` so no other javascript or script block will be modified.

This script block using the babel config above:
```
<script type="text/babel">
(...args) => console.log(...args)
</script>
```
will be transformed at compile time into:
```
<script type="text/javascript">
(function() { "use strict";

(function () {
  var _console;

  return (_console = console).log.apply(_console, arguments);
});
  
})();
</script>
```
