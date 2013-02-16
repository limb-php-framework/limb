# Шаг 6. Создание RSS-feed новостной ленты
RSS — неотъемлемая часть любого часто обновляемого проекта. В данном документе мы покажем, как можно создать RSS-feed при помощи Limb и MACRO.

Допустим, что нам необходимо сделать RSS-feed для последних новостей нашего туториала.

Создадим контроллер **RssController.class.php**, который будет делать вот что:

* получать последние новости,
* сортировать их по дате последней модификации,
* выставлять необходимые заголовки для RSS-ленты,
* передавать обработанные данные в MACRO шаблон, где будет формироваться XML.

Вот его исходный код:

    <?php
    lmb_require('limb/web_app/src/controller/lmbController.class.php');
 
    class RssController extends lmbController
    {
      function doDisplay() 
      {
        $params = array(
          'sort' => array('date' => 'DESC', 'title' => 'ASC'),
          'limit' => 5
        );
 
        $this->news = lmbActiveRecord::find('News', $params);
        $this->response->addHeader('Content-type: application/xml');
      }
    }

Выборка последних новостей уже не должна вызывать у вас никаких затруднений, в данном случае мы просто сортируем сразу по двум параметрам.

А вот строка добавления заголовка уже интереснее. Вы уже знакомы с объектом [запроса lmbHttpRequest](../../../../net/docs/ru/net/lmb_http_request.md) (доступным через $this→request в контроллере и шаблоне), аналогично можно работать и с объектом [ответа lmbHttpResponse](../../../../net/docs/ru/net/lmb_http_response.md). Здесь через метод addHeader добавляется HTTP-заголовок, необходимый для интерпретации отдаваемого сервером вывода как XML-документа.

Добавим шаблон template/rss/display.phtml. Вот он:

    <?xml version="1.0" encoding="UTF-8"?>
    <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
      <channel>
        <title>YourDomain.com last news</title>
        <language>en</language>
        <description>Last 5 news from our site</description>
        <link>{{route_url/}}</link>
        <atom:link href="{{route_url/}}" rel="self" type="application/rss+xml"/>
        {{list using="$#news" as="$item"}}
        {{list:item}}
        <item>
          <guid isPermaLink="false">g{$item->id}</guid>
          <title>{$item->title}</title>
          <link>{{route_url params="controller:news,action:detail,id:{$item->id}"/}}</link>
          <description>{$item->annotation}</description>
          <pubDate><?=date('r', strtotime($item->date));?></pubDate>
        </item>
        {{/list:item}}
        {{/list}}
      </channel>
    </rss>

Отметим несколько деталей:

    <?xml version="1.0" encoding="UTF-8"?>

MACRO корректно обрабатывает xml-заголовок при включенных [short tags](http://www.php.net/manual/en/ini.core.php#ini.short-open-tag), вызывающих конфликты в обычных php-скриптах.

    <link>{{route_url/}}</link>

Тег [{{route_url/}}](../../../../macro/docs/ru/macro/tags/lmb_request_tags/lmb_route_url_tag.md) вызванный без параметров, возвращает абсолютную ссылку на текущую страницу.

    <pubDate><?=date('r', strtotime($item->date));?></pubDate>

PHP-вставка выводит дату новости в нужном для RSS-формате.

В качестве финального штриха добавим ссылку на рсс-ленту в заголовок всех страниц нашего туториала, изменив шаблон template/page.phtml.

    <html>
    <head>
      <title>Limb3 tutorial</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="{{route_url params='controller:rss'}}"/>
    </head>
    <body bgcolor="#FFFFFF" text="#000000" >
      {{slot id="page_placeholder"/}}
    </body>
    </html>

Все! RSS-лента готова!

Спасибо, что дошли наш туториал до конца. Надеемся, что вам было интересно и познавательно.

## Далее

* [Шаг 7. Рекомендации по дальнейшему изучению](./step7.md)
