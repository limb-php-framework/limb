# Templates composition (including, wrapping and reusing)
{{macro}} has a rich set of tools for template composition, these are:

* **Including** — inserting the content of some other template into the current template
* **Wrapping** — wrapping a part or the whole content of the current template into some particular place(so called **slot**) or several places of another template
* **Reusing** — using one piece of the current template several times in the same template

**{{macro}} compiles the result of any composition into one PHP script** First {{macro}} processes composition tags such as {{include}}, {{wrap}}, {{into}}, {{apply}} and only then generates PHP script of the compiled template. That's why template composition cost you almost nothing in terms of execution speed. This feature of {{macro}} makes it possible to extract any duplicating parts into separate templates and reuse them efficiently in different ways.

## Including. Tag {{include}}
To include the contents of a template into particular point of the current template you can use [tag {{include}}](./tags/core_tags/include_tag.md).

For example:

    {{include file='header.phtml'/}}
    [...content...]
    {{include file='footer.phtml'/}}

**file** attribute of {{include}} tag contains either alias of template name or complete (absolute) file path. By default {{macro}} searches template in **template** folder relative to your application root folder. If your application is based on Limb3 WEB_APP package then {{macro}} will also be looking for templates in **template** folder of all Limb3 packages(this can be changed though, see LIMB_TEMPLATES_INCLUDE_PATH constant in limb/view/src/toolkit/lmbViewTools.class.php)

Please note that included template should be balanced. In {{macro}} balancing means that each opening tag should have a closing tag(if required) in the same template.

## Wrapping
### Basics of templates wrapping
Wrapping — is inserting a part of the current template into a particular place(slot) of the wrapper template(parent template)

There are three tags for wrapping:

* [Tag {{wrap}}](./tags/core_tags/wrap_tag.md) — tells which file should wrap the contents. Has opening and closing tags. If you need to wrap the whole content of the {{wrap}} tag into some particular wrapper template's slot then you can also specify it with into attribute.
* [Tag {{slot}}](./tags/core_tags/slot_tag.md) — marks a point in template where the contents of other templates can be inserted into.
* [Tag {{into}}](./tags/core_tags/into_tag.md) — specifies a portion of the current template that can be inserted into a slot in the wrapping template

Let's consider a small example.

**page.phtml** — parent template with slot:

    <html>
    <head>
    [...meta...]
    </head>
    <body>
     {{slot id='content'/}}
    </body>
    </html>

**my_page.phtml** — current template to be compiled:

    {{wrap with='page.phtml' into='content'}}
    [...content...]
    {{/wrap}}

As we already said, {{wrap}} tag must have a closing tag that limits a portion of the current template that will be inserted into a slot in parent template. **with** attribute of {{wrap}} tag specifies path to parent template or its alias. **with** is a required attribute of {{wrap}} tag. **into** attribute points at **id** attribute of the corresponding {{slot}} tag. **into** attribute can be skipped in case of multiple wrapping (see below).

As result {{macro}} will produce the following output:

    <html>
    <head>
    [...meta...]
    </head>
    <body>
     [...content...]
    </body>
    </html>

### Multiple wrapping. Tag {{into}}
Sometimes we need to insert several parts of the current template into different slots of parent template. For such cases you can use [tag {{into}}](./tags/core_tags/into_tag.md). A small example is worth of thousands words.

Let's suppose we have a base template **page.phtml**:

    <html>
    <body>
      {{slot id='content'/}}
    </body>
    </html>

Let's also suppose that we have an extra **layout.phtml** template with two slots:

    <div id='header'>{{slot id='header'/}}</div>
    <div id='main'>{{slot id='main'/}}</div>

And **display.phtml** as current template:

    {{wrap with='page.phtml' into='content'}}
 
    {{wrap file='layout.phtml'}}
     {{into slot='header'}}My Header{{/into}}
     {{into slot='main'}}My Complex content{{/into}}
    {{/wrap}}
 
    {{/wrap}}

The result of the compilation of display.phtml is:

    <html>
    <body>
       <div id='header'>My Header</div>
       <div id='main'>My Complex content</div>
    </body>
    </html>

How exactly {{macro}} compiles display.phtml? At the beginning the initial {{wrap}} tag is processed and we get:

    <html>
    <body>
      {{wrap with='layout.html'}}
       {{into slot='header'}}My Header{{/into}}
       {{into slot='main'}}My Complex content{{/core:wrap}}
      {{/wrap}}
    </body>
    </html>

Then the second {{wrap}} tag is processed and we get approximately the following intermediate compiled template:

    <html>
    <body>
 
     <div id='header'>{{slot id='header'/}}</div>
     <div id='main'>{{slot id='main'/}}</div>
 
     {{into slot='header'}}My Header{{/into}}
     {{into slot='main'}}My Complex content{{/into}}
 
    </body>
    </html>
    
Once {{into}} tags are processed we get the final result.

### Multiple wrapping and including
If {{into}} tag can't find necessary slot in template that is specified by parent {{wrap}}, it tries to find this slot starting from the root of the compile time tree. You can also use {{into}} tag inside included template without parent {{wrap}} tag in the same template. Here's a small example.

Let's suppose we have a base template **page.phtml**:

    <html>
    <body>
      <div class='content'>
      {{slot id='content'/}}
      </div>
      {{slot id='js_code'/}}
    </body>
    </html>

Let's also imagine that we have an extra **child.phtml** template with the following content:

    Insert me!
    {{into slot='js_code'}}<script type="text/javascript" src="..."></script>{{/into}}

And **display.phtml** as current template:

    {{wrap with='page.phtml' into='content'}}
    <div class='cool'>{{include file='child.phtml'}}</div>
    {{/wrap}}

The result is:

    <html>
    <body>
       <div class='content'>
        <div class='cool'>Insert me!</div>
       </div>
       <script type="text/javascript" src="..."></script>
    </body>
    </html>

As you can see, wrapping combinations can be of any complexity, be careful not to get lost in your templates ;)

## Reusing
There are situations when you need a portion of template to be rendered twice on the same page with different sets of data. For example, at a catalog page you need to print featured products, recently added products and first page of total list of products. Off course, you can use {{include}} tag but there is an alternative more simple way.

* [Tag {{template}}](./tags/core_tags/template_tag.md) — limits a portion of template that can be reused somewhere else. It's a kind of template inside template.
* [Tag {{apply}}](./tags/core_tags/apply_tag.md) — applies specified {{template}} tag

Here is an example:

    {{template name="photo_tpl"}}
      <span class="date">{$item.ctime|date:"d.m.Y"}</span>
      <a href="/photo/item/{$item.id}" class="img"><img alt="{$item.title}" src="{$item.icon_file_url}"></a>
      <a href="/photo/item/{$item.id}" class="title">{$item.title}</a>
      <a href="#" class="author">{$item.member.nick}</a>
      <a href="{$item.thumbnail_file_url}" title="{$item.title}" class='preview'>preview</a>
    {{/template}}
 
    <h2>Best photos in section {$#category.title}</h2>
    {{list using="$#best_photos" as="$photo"}}
    <ul id='best_photos_list'>
      {{list:item}}
       <li>{{apply template="photo_tpl" item="$photo"/}}</li>
      {{/list:item}}
    </ul>
    {{/list}}
 
    <h2>All photos in section {$#category.title}</h2>
    {{list using="$#photos" as="$photo"}}
    <ul id='photos_list'>
      {{list:item}}
       <li>{{apply template="photo_tpl" item="$photo"/}}</li>
      {{/list:item}}
    </ul>
    {{/list}}

{{template}} tag does not render anything without {{apply}}. In the compiled PHP class {{template}} tag creates an extra method and {{apply}} tag calls this method with passing any arguments into it(they will be available in the local data scope). In our example we passed **item** variable into {{template id='photo_tpl'}}
