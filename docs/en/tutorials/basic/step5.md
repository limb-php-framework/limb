# Step 5. Adding more functionality: news detail page, 5 latest news on the main page, sorting etc.
## Sorting news
Let's suppose we need to sort news by «date» field. This can be accomplished in many different ways. One possible way is to set sorting properties by **order** attribute of <active_record:fetch> tag right in the template:

    <active_record:fetch using='src/model/News' target='news' navigator='pager' order='date=DESC'/>

Another way is to call sort($params) method of newsline record set. You can use this method in case if you prefer using raw php-blocks in your templates:

    <?php 
       $news = lmbActiveRecord :: find('News')->sort('date' => 'DESC');
    ?>
 
    <list:LIST from="$news">
    [...]

One more way is to set default sorting parameters in News class:

    class News extends lmbActiveRecord
    {
      protected $_default_sort_params = array('date' => 'DESC');
    }

lmbActiveRecord :: $_default_sort_params allows you to set just default sorting params. If we passed any other sorting params using the first two methods — the default sorting params will be ignored.

## The latest news on the main page
Now when we know how to sort news we can render some latest news on the main page of our application. To do so let's create (or modify) **crud/template/main_page/display.html** template:

    <core:WRAP file="page.html" in="page_placeholder">
 
    <h1>Main page</h1>
 
    <active_record:fetch using='src/model/News' target="last_news" order='date=DESC' limit='3'/>
 
    <list:LIST id="last_news">
      <list:ITEM>
      <table>
      <tr>
        <td>{$date}</td>
        <td>{$title}</td>
      </tr>
      <tr>
        <td colspan='2'>{$annotation}
        <route_url params='controller:news,action:detail,id:{$id}'>more...</route_url>
        </td>
      </tr>
      </table>
      <hr/>
      </list:ITEM>
      <route_url params='controller:news'>all news...</route_url>
    </list:LIST>
 
    </core:wrap>

Here we used **limit** attribute of `<active_record:fetch>` tag. We can always limit the size of any record set fetched by `<active_record:fetch>` tag with **limit** and **offset** attributes.

There is an alternative way to apply limit and offset:

    <?php 
       $news = lmbActiveRecord :: find('News')->sort('date' => 'DESC')->paginate($offset = 0, $limit = 3);
    ?>

P.S. If you would like to see complete interface of news record set look at limb/core/src/lmbCollectionInterface.interface.php. lmbCollectionInterface is a common interface for all classes that implement collections (e.g. db record set or array holder lmbCollection).

The next line:

    <route_url params='controller:news,action:detail,id:{$id}'>more...</route_url>

is responsible for rendering links to pages with detail description of news.

If you started this tutorial with limb_app downloaded from SourseForge.net then you already have MainPageController. But if started this project from the scratch then you need to add a controller for the main page ( **crud/src/controller/MainPageController.class.php** ):

    <?php
    lmb_require('limb/web_app/src/controller/lmbController.class.php');
 
    class MainPageController extends lmbController
    {
    }
    ?>

## News detail page
Let's add new template **crud/template/news/detail.html** to render full information for some particular news:

    <core:WRAP file="page.html" in="page_placeholder">
 
    <active_record:fetch using='src/model/News' target="current_news" first="true">
      <fetch:params record_id='{$#request.id}'/>
    </active_record:fetch>
 
    <core:DATASOURCE id='current_news'>
 
    <h1>{$title}</h1>
 
    <b>Date</b> : {$date}<br/>
 
    {$content|raw}
 
    </core:DATASOURCE>
 
    </core:wrap>

Please note **first** attribute of `<active_record:fetch>` tag that means what we don't need a complete record set but just a single (first) record. `<core:datasource>` tag accepts news object fetched by `<active_record:fetch>` tag.

`<fetch:params>` tag is used to pass some extra params to `<active_record:fetch>` tag. In our case we used this tag to pass news identifier we need to load. The value of news identifier (id) we take from «request» — the request object with POST and GET data is available in the global scope in WACT template(this is done for you by WEB_APP package). {$#request.id} expression means: «use request from global scope then take 'id' attribute from request».

We can always achieve the same result with raw php-block without using any WACT tag for fetching operations:

    <core:WRAP file="page.html" in="page_placeholder">
 
    <?php $news = lmbActiveRecord :: findById('News', (int)$template->get('request')->get('id')); ?>
 
    <core:DATASOURCE from='$news'>
 
    <h1>{$title}</h1>
 
    <b>Date</b> : {$date}<br/>
 
    {$content|raw}
 
    </core:DATASOURCE>
 
    </core:wrap>

Or even without `<core:datasource>`:

    <core:WRAP file="page.html" in="page_placeholder">
 
    <?php $news = lmbActiveRecord :: findById('News', (int)$template->get('request')->get('id')); ?>
 
    <h1>{$$news.title}</h1>
 
    <b>Date</b> : {$$news.date}<br/>
 
    {$$news.content|raw}
 
    </core:wrap>

The last point is to modify news/display.html a bit to render a link to news detail page as well as on the main page:

    [...]
    <tr>
      <td colspan='4'>
        {$annotation}
        <route_url params='controller:news,action:detail,id:{$id}'>more...</route_url>
      </td>
    </tr>
    [...]

## What's next?
[Step 6. Creating newsline RSS-feed](./step6.md)
