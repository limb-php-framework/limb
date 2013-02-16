# Step 6. Creating newsline RSS-feed
RSS — is an essential feature of any frequently updated web project. At this page we will show you how to create RSS feeds with Limb3.

Let's suppose we need to create RSS-feed with the latest news. To do so we'll create **last-news-feed.php** script and will put it into **crud/www** folder of our project.

The algorithm of **last-news-feed.php** is the following:

* get the latest news
* make absolute hrefs for news pages
* push news record set to WACT template and convert it into XML
* return RSS XML to client.

The source code of last-news-feed.php:

    <?php
    require_once('../setup.php');
    require_once('limb/web_app/src/template/lmbWactTemplate.class.php');
    require_once('limb/core/src/lmbCollection.class.php');
    require_once('src/model/News.class.php');
 
    $template = new lmbWactTemplate('rss/last_news.rss');
    $template->setChildDataSet('last_news', getNewsDataSetWithFullPaths());
 
    header("Content-Type: application/xml");
 
    $template->display();
 
    function getNewsDataSetWithFullPaths()
    {
      $news_rs = lmbActiveRecord :: find('News', array('sort' => array('date' => 'DESC', 'title' => 'ASC'),
                                                       'limit' => 5));
      $result = array();
      foreach($news_rs as $news)
      {
        $news_id = $news->getId();
        $result[$news_id] = $news->export();
        $result[$news_id]['path'] = 'http://' . $_SERVER['HTTP_HOST'] . '/news/detail/' . $news_id;
      }
 
      return new lmbCollection($result);
    }
    ?>

Let's explain what happens in the script above. The most interesting part is getNewsDataSetWithFullPaths() function where news are loaded from database using lmbActiveRecord :: find() method. News record set is sorted by «date» and «title» and limited with 5 elements. lmbActiveRecord :: find() method accepts class name of objects to be loaded and an array of parameters to be applied to the retrieved record set.

By the way, lmbActiveRecord :: find(..) part can be rewritten in a slightly different way using methods chaining:

    $news_rs = lmbActiveRecord :: find('News')->sort(array('date' => 'DESC', 'title' => 'ASC'))->paginate(0, 5);

Once news are loaded we need to create a collection that contains news data as well as absolute paths to every news page:

    $result = array();
    foreach($news_rs as $news)
    {
      $news_id = $news->getId();
      $result[$news_id] = $news->export();
      $result[$news_id]['path'] = 'http://' . $_SERVER['HTTP_HOST'] . '/news/detail/' . $news_id;
    }
    return new lmbCollection($result);

lmbCollection class is used above to create an iterator to be passed into WACT view.

Then WACT instance is created using rss/last_news.rss template followed by news collection passed into «last_news» runtime component of `<list:list>` tag:

    $template = new lmbWactTemplate('rss/last_news.rss');
    $template->setChildDataSet('last_news', getNewsDataSetWithFullPaths());

Here is rss/last_news.rss template contents:

    <!--l version="1.0" encoding="utf-8"-->
    <!DOCTYPE rss [<!ENTITY % HTMLlat1 PUBLIC "-//W3C//ENTITIES Latin 1 for XHTML//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml-lat1.ent">]>
    <rss version="0.92" xml:base="http://tutorial.dbrain.bit">
 
    <channel>
     <title>YourDomain.com last news</title>
     <language>en</language>
 
    <list:list id='last_news'>
     <list:item>
      <item>
       <title>{$title}</title>
       <link>{$path}</link>
       <description>
          {$annotation}
       </description>
       <pubDate>
          {$date}
       </pubDate>
     </item>
      </list:item>
    </list:list>
    </channel>
    </rss>

Please note how proper mime type is sent to the client:

    header("Content-Type: application/xml");

Finally let's add a link to this RSS-feed into crud/template/page.html template:

    <html>
    <head>
      <title>Limb3 tutorial</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body bgcolor="#FFFFFF" text="#000000" >
      <a href="/last-news-feed.php">RSS-feed</a>
      <hr />
      <core:PLACEHOLDER id="page_placeholder"/>
    </body>
    </html>

## What's next?
[Step 7. Further readings](./step7.md)
