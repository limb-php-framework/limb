# MACRO package ( {{macro}} template engine )
We have been using and developing our own forked version of [WACT-а](http://www.phpwact.org/) template engine for 5+ years and been pretty happy about it. However, WACT was built on two core principles which appeared to be problematic for templates with complex visualization logic. These principles are:

* XML-alike syntax.
* Data contexts (data sources or scopes) that are used to display data from.

An essential part of WACT documentation is dedicated to contexts. XML-like syntax does not allow you to use WACT tags as values of regular tags attributes and also makes internal WACT architecture very complex that is hard to support and extend.

Thus the idea of {{macro}} appeared.

{{macro}} is built on the following principles:

* No more XML-like syntax. Tags are marked with two curly brackets on both sides, e.g. {{include}}.
* There are two data scopes only: global and local.
* No more data sources - all data is fetched from plain PHP variables.
* Keep it as simple as possible
* Support all WACT features like templates wrapping, including

Just like WACT, {{macro}} compiles initial templates into an executable PHP-script with very clean syntax (much cleaner than WACT or Smarty) and executes them very fast (we will publish benchmark results later).

Our forked version of WACT will be supported by our team for a while but it's going to be replaced by {{macro}} eventually.

For those who worked with WACT templates before there should be no problem switching to {{macro}}. If you are new to {{macro}} we recommend you to read all pages in this section marked «For {{macro}} beginners».

## For {{macro}} template authors

* [Tags dictionary](./macro/tags_intro.md)
* [Filters dictionary](./macro/filters_intro.md)

## For {{macro}} beginners

* **Intro**
  * [Pros and cons of {{macro}}](./macro/pros_and_cons.md)
  * [Introduction to {{macro}}. {{macro}} templates syntax and entities](./macro/intro.md)
  * [{{macro}} compilation and rendering. How to run {{macro}} template](./macro/important_details.md)
  * [How {{macro}} template gets data for displaying?](./macro/data_sources.md)
  * **The main elements of {{macro}} templates**
      * [Tags](./macro/tags_info.md)
      * [Expressions. How to render variable values in templates](./macro/expressions.md)
      * [Filters (or formatters). How to modify or format data on rendering](./macro/filters_intro.md)
      * [PHP code in {{macro}} templates](./macro/php_code_in_templates.md)
* **Basic {{macro}} use cases**
  * [Rendering lists or tables](./macro/list_tags.md)
  * [Rendering lists with pagers. Pagination](./macro/pagination.md)
  * [Forms and form elements](./macro/form_tags.md)
  * [Templates composition (including, wrapping and reusing)](./macro/template_composition.md)
* **Some {{macro}} internals**
  * [{{macro}} dictionaries (supported tags and filters. Where {{macro}} looks for tags and filters)](./macro/dictionaries.md)
  * [How {{macro}} searches templates by aliases](./macro/template_file_name_resolving.md)
  * [{{macro}} error messages and templates debugging](./macro/errors_and_debug.md)

## For {{macro}} developers

* [{{macro}} compiler](./macro/compiler.md)
* [How to create your own tags](./macro/how_to_create_new_tag.md)
* [How to create your own filters](./macro/how_to_create_new_filter.md)
