# Step 4. Templates optimization. Adding news pagination
## Extracting form_fields.html and form_errors.html templates
If you noticed our templates news/create.html and news/edit.html have a lot of duplication. Let's use [<core:include>](../../../../macro/docs/en/macro/tags/core_tags/include_tag.md) tag to extract some common template code into separate files. `<core:include>` works almost just like php include() function.

First we extract **news/form_fields.html** template with the following contents:

    <label for="title">Title</label> : <input name='title' type='text' size='60' title='Title'/><br/>
    <label for="date">Date</label> : <input name='date' type='text' size='15' title='Date'/><br/>
    <label for="annotation">Annotation</label> : <textarea name='annotation' rows='2' cols='40' title='Annotation'></textarea><br/>
    <label for="content">Content</label> : <textarea name='content' rows='5' cols='40'></textarea><br/>

Second is **news/form_errors.html** with the code that renders form errors:

    <form:errors target='errors'/>
 
    <list:list id='errors'>
    <table>
    <list:ITEM>
        <tr valign="top">
          <td width="20%">&nbsp;</td>
          <td width="80%"><FONT COLOR="RED">{$ErrorMessage}</FONT></td>
        </tr>
    </list:ITEM>
    </table>
    </list:list>

Now we modify news/create.html and news/edit.html to use two new templates:

    <html>
    <head>
      <title>Limb3 tutorial</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body bgcolor="#FFFFFF" text="#000000" >
 
    <h1>Create news</h1>
 
    <form id='news_form' name='news_form' method='post' runat='server'>
 
    <core:include file="news/form_errors.html" />
 
    <core:include file="news/form_fields.html" />
 
    <input type='submit' value='Create' runat='client'/>
    </form>
 
    </body>
    </html>

and

    <html>
    <head>
      <title>Limb3 tutorial</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body bgcolor="#FFFFFF" text="#000000" >
 
    <h1>Edit news</h1>
 
    <form id='news_form' name='news_form' method='post' runat='server'>
 
    <core:include file="news/form_errors.html" />
 
    <core:include file="news/form_fields.html" />
 
    <input type='submit' value='Edit' runat='client'/>
    </form>
 
    </body>
    </html>

Much better! But we can do more with WACT and make our templates even more compact.

Note: WACT compiles templates as a whole. That means what `<core:include>` usage doesn't slow template rendering unlike Smarty.

## Extracting core template page.html
The template code in news/create.html and news/edit.html before starting `<form>` and after closing `</form>` tags are duplicating too. With other template engine we would extract something like header.html and footer.html and would use `<core:include>` for them. eader/footer way has a big disadvantage that it breaks the template into two components that complicate common understanding of the template as a whole. But WACT has better means.

Let's use [`<core:wrap>`](../../../../macro/docs/en/macro/tags/core_tags/wrap_tag.md) tag that allows to insert a part of the current template into some special placeholder in some other template (core or parent template). This process we call **wrapping**.

Ok, let's create a template crud/template/page.html with the following contents:

    <html>
    <head>
      <title>Limb3 tutorial</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body bgcolor="#FFFFFF" text="#000000" >
      <core:PLACEHOLDER id="page_placeholder"/>
    </body>
    </html>

Note `<core:placeholder>` tag that declares a place where some part of over template can be inserted into.

Now it's time to modify news/create.html and news/edit.html as follows:

    <core:wrap file="page.html" in="page_placeholder">
 
    <h1>Create news</h1>
 
    <form id='news_form' name='news_form' method='post' runat='server'>
 
    <core:include file="news/form_errors.html" />
 
    <core:include file="news/form_fields.html" />
 
    <input type='submit' value='Create' runat='client'/>
    </form>
 
    </core:wrap>

and

    <core:wrap file="page.html" in="page_placeholder">
 
    <h1>Edit news</h1>
    <form id='news_form' name='news_form' method='post' runat='server'>
 
    <core:include file="news/form_errors.html" />
 
    <core:include file="news/form_fields.html" />
 
    <input type='submit' value='Edit' runat='client'/>
    </form>
    </core:wrap>

`<core:wrap>` tag has **file** attribute, that names a relative template path of the core template and **in** attribute, that names a placeholder identifier (id attribute). `<core:wrap>` has both opening and closing tags that marks a part of the current template that must be inserted into the parent template.

In fact `<core:wrap>` and `<core:include>` tags give you a lot of opportunities for templates optimization like multiple wrapping, parameter passing etc. but these opportunities and the principles of their usage is beyond the scope of this tutorial.

## Adding news pagination
The news list can be too large to display on a single page. In this case, the list can be splitted into pages (so called pagination) with a certain number of news on one page. Pagination is fairly simple with WACT.

Let's create a new template **crud/template/pager.html** with the following contents:

    <pager:NAVIGATOR id="pager" items="3">
 
    Shown: from <b>{$BeginItemNumber}</b> to <b>{$EndItemNumber}</b>
 
    <pager:FIRST><a href="{$href}">|--</a></pager:FIRST>
 
    <pager:LIST>
 
    <pager:CURRENT><b><a href="{$href}">{$number}</a></b></pager:CURRENT>
    <pager:NUMBER><a href="{$href}">{$number}</a></pager:NUMBER>
 
    </pager:LIST>
 
    <pager:LAST><a href="{$href}">--|</a></pager:LAST>
 
    Total: <b>{$TotalItems}</b>
    </pager:NAVIGATOR>

The main tag here is `<pager:NAVIGATOR>` that bounds the pager. items attribute of `<pager:NAVIGATOR>` sets a number of items of a single page. The rest of the `<pager:…>` tags just outputs links to first (`<pager:FIRST>` tag), previous (`<pager:PREV>` tag, now used here), current(`<pager:CURRENT>` tag), next(<pager:NEXT> tag, not used here) and last(`<pager:LAST>` tag) pages, and also links to pages in between (`<pager:NUMBER>` tag).

With output expressions {$TotalItems}, {$BeginItemNumber} and {$EndItemNumber} that can be used inside `<pager:NAVIGATOR>` we render the total number of items in the list and number of the first and the last items on the current page. {$TotalItems}, {$BeginItemNumber} and {$EndItemNumber} are so called tag properties. In order to distinct tag properties from regular output expressions with variables it's better to use slightly modified syntax: {$:TotalItems}, {$:BeginItemNumber} and {$:EndItemNumber}.

Now we need to include pager.html into news/display.html and to wire newsline record set with pager in order to start pagination. We also modified news/display.html to wrap it into page.html as with news/create.html and news/edit.html:

    <core:WRAP file="page.html" in="page_placeholder">
 
    <h1>Newsline</h1>
 
    <route_url params="action:create">Create news</route_url>
    <p/>
 
    <active_record:fetch using='src/model/News' target="news" navigator='pager'/>
 
    <core:include file='pager.html' />
 
    <list:LIST id="news">
      <table border="1">
      <tr>
        <th>ID</th>
        <th>Date</th>
        <th>Title</th>
        <th>Actions</th>
      </tr>
      <list:ITEM>
      <tr>
        <td>#{$id}</td>
        <td>{$date}</td>
        <td>{$title}</td>
        <td>
          <route_url params="action:edit,id:{$id}">Edit</route_url>&nbsp;&nbsp;
          <route_url params="action:delete,id:{$id}">Delete</route_url>
        </td>
      </tr>
      <tr>
        <td colspan='4'>
          {$annotation}
        </td>
      </tr>
      </list:ITEM>
      </table>
    </list:LIST>
 
    </core:wrap>

Note new **navigator** attribute of `<active_record:fetch>` tag that names the identifier of our `<pager:navigator>` tag. This is how we wired bind fetched record set with pager.

If you did everything right, then you should get something like this:

![Alt-main_page](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:basic:main_page.png)

## What's next?
[Step 5. Adding more functionality: single news in detail, 5 latest news on the main page, sorting etc.](./step5.md)
