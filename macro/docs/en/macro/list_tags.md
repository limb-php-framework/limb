# Rendering lists or tables
## {{macro}} tags for lists rendering
Let's consider the following example:

     <table border="1">
       <tr>
        <th>№</th>
        <th>Title</th>
        <th>Volume, liters</th>
        <th>Weight, kg</th>
       </tr>
     {{list using="$#tanks" as="$tank" counter="$number"}}
       {{list:item}}
       <tr>
        <td>{$number}</td>
        <td>{$tank.name}</td>
        <td>{$tank.volume}</td>
        <td>{$tank.weight}</td>
       </tr>
       {{/list:item}}
      {{list:empty}}
      <tr><td colspan='4'>List is empty!</td></tr>
      {{/list:default}}
     {{/list}}
    </table>

To render a list or a table with {{macro}} you will need a group of ListTags:

* [Tag {{list}}](./tags/list_tags/list_tags.md) — main tag that renders its content if a variable named by **using** attribute is a non empty array or Iterator. **as** attribute tells {{list}} tag name of the temporary variable that will contain a reference to every next element of the list. By default **as** attribute has **$item** value.
* [Tag {{list:item}}](./tags/list_tags/list_item_tag.md) — repeats its content for every element of the list.
* [Tag {{list:empty}}](./tags/list_tags/list_empty_tag.md) — renders its content in case the list is empty.
* [Tag {{list:glue}}](./tags/list_tags/list_glue_tag.md) — used to render a portion of the template to separate elements in the list.
* **$number** variable that contains row number of the list. $number variable can be changed using **counter** attribute for {{list}} tag.

There can be two different kinds of rendering results: for non empty lists and for empty lists:

Non empty:

| № | Title	| Volume, liters | Weight, kg |
|---|-------|----------------|------------|
| 1	| Tank AB-102	| 2400 | 340 |
| 2	| Tank AB-103	| 2000 | 300 |

Empty:

| № | Title  | Volume, liters | Weight, kg |
|---|-------|----------------|------------|
| List is empty! |

## Rendering multi-column lists
To render a multi-column list (for example 3 items in a row) [tag {{list:glue}}](./tags/list_tags/list_glue_tag.md) is used that can render its content once per several steps. The value of step can be set by a **step** attribute of the {{list:glue}} tag.

For example:

    {{list using="$#images"}}
    <table>
    <tr>
       {{list:item}}
       <td>
        <img src='{$item.path}' border='0' /><br />{$item.title}
       </td>
       {{list:glue step="3"}}</tr><tr>{{/list:glue}}
       {{/list:item}}
    </tr>
    </table>
    {{/list}}

This template will output images in 3 columns.

As you may have noticed the example above will produce invalid HTML layout for lists with number of elements not evenly divisible by 3. Let's fix this by applying [tag {{list:fill}}](./tags/list_tags/list_fill_tag.md):

    {{list using="$#images"}}
    <table>
    <tr>
      {{list:item}}
        <td>
         <img src='{$item.path}' border='0' /><br />{$item.title}
        </td>
       {{list:glue step="3"}}</tr><tr>{{/list:glue}}
     </list:item>
     {{list:fill upto='3' items_left='$items_left'}}
      <td colspan='{$items_left}'>&nbsp;</td>
     {{/list:fill}}
    </tr>
    </table>
    {{/list}}

Tag {{list:fill}} outputs its contents only if list has a number of elements more than zero but up to some value that is specified by **upto** attribute. {{list:fill}} also fills a variable named according to **items_left** attribute that holds a number of items required to produce a valid layout.

You can also consider using [tag {{repeat}}](./tags/core_tags/repeat_tag.md) with {{list:fill}} to render missing items:

    {{list using="$#images"}}
    <table>
    <tr>
     {{list:item}}
        <td>
         <img src='{$item.path}' border='0' /><br />{$item.title}
        </td>
       {{list:glue step="3"}}</tr><tr>{{/list:glue}}
     {{/list:item}}
     {{list:fill upto='3' items_left='$some_var'}}
       {{repeat times='{$some_var}'}}
       <td>
        <img src='/images/no_image.gif' alt='sorry, no image' />
       </td>
       {{repeat}}
     {{/list:fill}}
    </tr>
    </table>
    {{/list}}

## More examples

* See examples for [tag {{list}}](./tags/list_tags/list_tags.md)
* See examples for [tag {{list:glue}}](./tags/list_tags/list_glue_tag.md)
