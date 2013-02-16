# Pros and cons of MACRO template engine
## Pros

* MACRO has **powerful tools for templates composition** such as wrapping, including, reusing, multiple wrapping. We really don't know any other template engine that offers the same capabilities.
* MACRO is a **compiling template engine** where initial template is compiled into native PHP code. Unlike Smarty, MACRO compiles templates into one PHP script. This means that compiled MACRO template is always fast and all includes and wraps cost you nothing in terms of execution speed.
* MACRO has **just a small set of restrictive rules**. You can use regular PHP blocks inside MACRO template, you can use any helpers, etc. You can use MACRO tags or not - it's up to you. We really tried hard to find the best possible balance between ease of use of raw PHP code in templates and MACRO tags.
* You can always **extend MACRO** with your own components such as tags and filters.
* MACRO **compiles** its elements **into very clean PHP code**, much cleaner than Smarty or WACT. This makes MACRO to be a really **fast template engine**. And you can always create your own MACRO tags to produce even more optimized PHP code.
* You can write your own template resolving mechanism or implement you own MACRO configuration schema.
* MACRO **suits perfectly for HTML coders** since it has a rich set of tools for rendering tables, pager, forms, etc. HTML coders can do most of their job without any help of PHP programmers.

## Cons

* It's something new you have never used before.
* Anything else? ;) Let's discuss it at [our forum](http://forum.limb-project.com/).
