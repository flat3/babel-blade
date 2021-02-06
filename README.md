# Babel compiler for Laravel Blade

This package allows you to safely write modern, inline JavaScript in blade templates.
Compilation only happens at Blade template caching time, so has no effect on production performance.

## Installation

To install the package via composer:

`composer require --dev flat3/babel-blade`

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

## Usage

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

## Polyfills

Babel implements async/await using generators, which need to be polyfilled on older platforms.
Without the polyfill you'll see the error:
```
ReferenceError: Can't find variable: regeneratorRuntime
```
To solve this you can include an additional script tag that includes the polyfill, for example:
```
<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/7.12.1/polyfill.min.js"></script>
```

## Blade directives

Because babel-blade runs early in the compilation process, directives such as `@json($src)` haven't been parsed yet and will be passed to Babel as they are.

You will then get failures such as ```Support for the experimental syntax 'decorators-legacy' isn't currently enabled``` as Babel tries to parse `@json`.

There is likely not a solution to this due to the order of execution in blade, instead use the style `"{{$src}}" with appropriate escaping.`

## License

Copyright © Chris Lloyd

Flat3 babel-blade is open-sourced software licensed under the [MIT license]