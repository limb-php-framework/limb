# Introduction to {{macro}}. {{macro}} templates syntax and entities.
This page demonstrates a simple example of {{macro}} template and covers briefly {{macro}} syntax and template entities like tags, output expressions and filters.

## {{macro}} template example

    {{wrap with="page.phtml" in="content_zone"}}
    <img src={$#photo.largeFileUrl} />
    <dl>
    <dt>Author:</dt><dd>{$#photo.member.name}</dd>
    <dt>Category:</dt><dd>{$#photo.category.title}</dd>
    <dt>Title:</dt><dd>{$#photo.title}</dd>
    <dt>Tags:</dt>
    <dd>
    {{list using='{$#photo.tags}' as='$tag'}}
      <ul>
      {{list:item}}
        <li>{$tag.title|uppercase}</li>
      {{/list:item}}
      </ul>
      {{list:default}}
      No tags.
      {{/list:default}}  
    {{/list}}
    </dd>
    {{include file="photo/marks.phtml"}}
    </dl>
    {{/wrap}}

## {{macro}} template entities
### Tags
There are several **tags** in our example:

    {{wrap with="page.phtml" in="content_zone"}}...{{/wrap}}
 
    {{list using='{$#photo.tags}' as='$tag'}}...{{/list}}
 
    {{include file="photo/marks.phtml"/}}

Tags usually have a closing tag (e.g **list** or **wrap**), but there also tags that don't have a closing tag like **slot**.

Tags can have several **attributes**. In our example tag **list** has **using** and **as** attributes.

There are rules that are applied to tags. For example, some tags should be used inside another particular tag in the template, some tags have required attributes, etc. In most cases macro compiler will warn you about such errors and will display template file path and line number where error has appeared.

{{macro}} already has a [lot of tags](./tags_intro.md) for most every-day tasks like rendering lists or tables, working with forms and form elements, any kind of templates composition, lists pagination etc.

[More about tags](./tags_intro.md).

## Output expressions
Output expressions (or just expressions) are used to output variable values.

In our example expressions are:

    {$tag.title|uppercase}
    {$#photo.largeFileUrl}

You can think about output expression as of PHP «echo» operator since {{macro}} actually compiles output expressions into <? echo $variable_name; ?> constructions.

The symbol of grid(which means global) before variable name tells {{macro}} to convert {$#var_name} into {$this→var_name}. {{macro}} compiles template into the unique PHP class(more about this you can read at [{{macro}} compilation and rendering. How to run {{macro}} template](./important_details.md)) Thus expression {$#var_name} simply echos some attribute of the generated class.

Expressions can also be used as tag attributes values in some cases. In such cases expressions just reference variables and not rendered.

There are also so called «dotted» expressions with dots between elements. For example, {$tag.title} expression will be actually rendered into something like <?php if(isset($tag['title'])) echo $tag['title']; ?>

[See more about expressions](./expressions.md).

## Filters or formatters
Filters (sometimes called formatters) are used to modify/format variables during rendering. Filters are applied to output expressions.

In our example:

    {$tag.title|uppercase}

* uppercase — is a simple filter for rendering variable value in UPPERCASE. You can think about this filter as of an alias for PHP function strtoupper(), that is applied to $tag.title value.

In most cases filters are just wrappers for commonly used PHP functions.

[More about filters](./filters_intro.md).

## Further reading

* [{{macro}} compilation and rendering. How to run {{macro}} template.](./important_details.md) — you will know how {{macro}} actually ticks. This page is a key for effective {{macro}} usage.
* [How {{macro}} template gets data for displaying?](./data_sources.md)
