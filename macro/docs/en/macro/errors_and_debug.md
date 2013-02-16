# {{macro}} error messages and templates debugging
## Template compilation errors

{{macro}} compiler can detect the most of errors in templates.

Here is the list of errors that are considered to be fatal for a {{macro}} template:

* Unbalanced template, that has an opening tag in one template and does not have a corresponding closing tag in the same template file(in case if this tag requires a closing tag).
* Missing a required attribute for a tag
* Improper nesting of {{macro}} tags. For example you can't use {{list:item}} tag outside {{list}} tag
* Having two or more child tags of one parent tag with the same id attribute.
* Usage of none existing file with [tag {{include}}](./tags/core_tags/include_tag.md) or [tag {{wrap}}](./tags/core_tags/wrap_tag.md).
* Usage of none existing filters in expressions
* Usage of none existing tags

{{macro}} compiler tells you about error with short message and points at the template file and line number where error occurred.

## Compiled template
You can find all compiled {{macro}} templates in **var/compiled/** folder of your application. If you have a template that throws a error at runtime then you may want to switch off template recompilation and insert debugger break command(e.g. debugBreak(), for [dbg](http://www.php-debugger.com/dbg/)) at the problematic point of compiled template. In some cases just a short glance at the compiled PHP code is enough to understand the source of problem in template.
