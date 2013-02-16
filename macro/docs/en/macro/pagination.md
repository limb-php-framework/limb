# Rendering lists with pagers. Pagination
In most cases lists are too long to be displayed on a single page. In such cases lists are usually spllited(paginated) into many small pages.

**Pager** — is a bunch of links that allow visitors to jump to other pages of displayed list. Pager may come in different forms: it can be just two links «next page» and «previous page», a complete number of available pages or any other variant.

{{macro}} has number of tools to simplify the process of pagination.

Standard pagination has two tasks:

* display a pager.
* limit paginated list to display only a certain number of items that is corresponding to selected(or current) page of a pager.

## Displaying a pager
To display a pager you can use {{macro}} [tag {{pager}}](./tags/pager_tags/pager_tag.md).

Here is an example:

    {{pager id="my_pager" items="10" total_items="$#total_items_in_list"}}
 
    Show items: from <b>{$begin_item_number}</b> to <b>{$end_item_number}</b>
 
    {{pager:first}}<a href="{$href}">First page </a>{{/pager:first}}
 
    {{pager:list}}
   
    {{pager:current}}<b><a href="{$href}">{$number}</a></b>{{/pager:current}}
    {{pager:number}}<a href="{$href}">{$number}</a>{{/limb:pager:number}}
 
    {{/pager:list}}
 
    {{pager:last}}<a href="{$href}">Last page</a>{{/pager:last}}
 
    Total items: <b>{$total_items}</b>
    {{/pager}}

The main pager tag is [tag {{pager}}](./tags/pager_tags/pager_tag.md) that limits pager. **items** attribute of {{pager}} tag sets maximum number of items that can be displayed on a single page. **total_items** attribute allows to set total number of items in the paginated list. {{pager}} fetches current page number from predefined PHP $_GET variable automatically.

[Tag {{pager:list}}](./tags/pager_tags/pager_list_tag.md) renders links to pages including current page.

Other tags used in the example above are:

* [Tags {{pager:prev}}, {{pager:first}}, {{pager:next}}, {{pager:last}}](./tags/pager_tags/pager_frontier_tag.md) — outputs links to previous, first, next and last pages of pager.
* [Tag {{pager:current}}](./tags/pager_tags/pager_current_tag.md) — outputs link to or just a number of current page,
* [Tag {{pager:number}}](./tags/pager_tags/pager_number_tag.md) — outputs link to some particular pager page.

Each tag creates a local available variable **$href** that contains an URL of corresponding page. $href value has the following form: CURRENT_URI[?|&]pager_id_pager=$page_number. Tag {{pager:number}} and {{pager:current}} also generate and fill **$number** variable with corresponding page number.

There are also several variables available inside {{pager}} tag:

* **{$total_items}** — total number of items in paginated list,
* **{$total_pages}** — total number of pages,
* **{$begin_item_number}** — number of item that is starting current page,
* **{$end_item_number}** — number of item that is finishing current page.

It's a common practice to create one or more pagers and include them close by lists using [tag {{include}}](./tags/core_tags/include_tag.md).

## Limiting paginated lists
### Limiting paginated lists in non Limb3 based applications
{{pager}} tag actually creates a helper object of **lmbMacroPagerHelper** class at runtime that performs all pagination logic. You can always access this object right in {{macro}} template by {$this→pager_PAGER_ID} where PAGER_ID is value of **id** attribute of {{pager}} tag.

Here is list of method you may need:

* **setTotalItems($number)** — sets a total number of items in a paginated list
* **setItemsPerPage($number)** — sets a maximum number of items that can be displayed on a single page
* **getItemsPerPage()** — returns a maximum number of items that can be displayed on a single page. Can be used as a limit for a paginated list.
* **prepare()** — should be called after setTotalItems() or setItemsPerPage() to recalculate a pager.
* **getCurrentPageOffset()** — returns a value that can be used as an offset for a paginated list (not available in 2007.4, for 2007.4 consider using getCurrentPageBeginItem())
* **getCurrentPageBeginItem()** — returns a number of first item to be displayed on the current page (you need to extract 1 from this value for positive values since this value is not zero based).

### Limiting paginated lists in Limb3 based applications
There is also [tag {{paginate}}](./tags/pager_tags/paginate_tag.md) that can greatly simplify limitation of paginated list. {{paginate}} tag has only one condition: paginated list should be an object that supports **lmbCollectionInterface** interface (see Limb3 CORE package).

Let's consider the following {{macro}} template:

    {{paginate iterator='$#modules' pager='my_pager'/}}
 
    {{pager id="my_pager" items="5"}}
        {{pager:first}}<a href='{$href}'>First</a>{{/pager:first}} {{pager:prev}}<a href='{$href}'>Prev</a>{{/pager:prev}}
        {{pager:list}}
         [...]
        {{/pager:list}}
        {{pager:next}}<a href='{$href}'>Next</a>{{/pager:next}} {{pager:last}}<a href='{$href}'>Last</a>{{/pager:last}}
    {{/pager}}
 
    {{list using='$#modules'>
      <TABLE width="100%" BORDER="1" ALIGN="CENTER">
        {{list:item}}
          <TR>
            <TD>{$item.name}</TD>
            <TD>{$item.description|default:"&nbsp;"}</TD>
          </TR>
        {{/list:item}}
      </TABLE>
    {{/list}}

Note **iterator** attribute value of {{paginate}} tag and **using** attribute of {{list}} tag. They point at the same variable. **pager** attribute of {{paginate}} tag has the same value as **id** attribute of {{pager}} tag. You should also make sure that iterator is filled before {{paginate}} tag is executed. That means you need to perform pull-data operations BEFORE {{paginate}} tag.

## What if I have REALLY MANY PAGES in pager?
If you have a really long list with too many pages in pager to display there are two available workarounds:

* Use ellipses (or gaps) — in this case only certain number of pages in pager will be displayed: some at the beginning, some in the middle and some at the end of pager. Pager with ellipses looks approximately like this: 1-2-3…7-8-9-10-11…17-18-19
* Use sections (or blocks of pages) — is this case pager will display only a small portion of pages (current section) and other blocks (section) will be collapsed. Pager with sections looks approximately like this: [1-5]6-7-8-9-10[11-15][16-19].

See [tag {{pager}}](./tags/pager_tags/pager_tag.md) description.

### Ellipses
[Tag {{pager:elipses}}](./tags/pager_tags/pager_elipses_tag.md) is used to render ellipses. You may also need a couple of extra attributes for {{pager}} tags: * **pages_in_middle** * **pages_in_sides**.

For example:

    {{pager id="pager" items="5" pages_in_middle="5" pages_in_sides="3"}}
 
    {{pager:list}}
    {{pager:current}}<b><a href="{$href}">{$number}</a></b>{{/pager:current}}
    {{pager:number}}<a href="{$href}">{$number}</a>{{/pager:number}}
    {{pager:elipses}}...{{/pager:elipses}}
    {{pager:separator}}-{{/pager:separator}}
    {{/pager:list}}
 
    {{/pager}}

Will produce:

    1-2-3...6-7-8-9-10...15-16-17

### Sections
[Tag {{pager:section}}](./tags/pager_tags/pager_section_tag.md) is used to render blocks of pages. Also **pages_per_section** attribute of {{pager}} tag is used.

Example:

    {{pager id="pager" items="5" pages_per_section="5"}}
 
    {{pager:list}}
    {{pager:section}}<a href="{$href}">[{$section_begin_number}..{$section_end_number}]</a>{{/pager:section}}
    {{pager:current}}<b><a href="{$href}">{$number}</a></b>{{/pager:current}}
    {{pager:number}}<a href="{$href}">{$number}</a>{{/pager:number}}
    {{pager:separator}}-{{/pager:separator}}
    {{/pager:list}}
 
    {{/pager}}

Tag {{pager:section}} also creates **$section_begin_number** and **$section_end_number variables**.

The example above will produce the following pager:

    [1..5][6..10]11-12-13-14-15[16..17]

## More examples

* See more examples at [tag {{pager}}](./tags/pager_tags/pager_tag.md).
