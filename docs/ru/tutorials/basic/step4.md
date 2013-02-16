# Шаг 4. Оптимизация шаблонов. Добавление постраничного вывода
## Вынесение шаблонов form_fields.phtml и form_errors.phtml из шаблонов форм
Поля формы и ошибки валидации в шаблонах template/news/create.phtml и template/news/edit.phtml выводятся полностью одинаково. Воспользуемся тегом [{{include}}](../../../../macro/docs/ru/macro/tags/core_tags/include_tag.md), который является как бы аналогом функции include PHP.

Создадим шаблон template/news/form_fields.phtml следующего содержания:

    {{label for="title"}}Title{{/label}} : {{input name='title' type='text' size='60' title='Title'/}}<br/>
    {{label for="date"}}Date{{/label}} : {{input name='date' type='text' size='15' title='Date'/}}<br/>
    {{label for="annotation"}}Annotation{{/label}} : {{textarea name='annotation' rows='2' cols='40' title='Annotation'/}}<br/>
    {{label for="content"}}Content{{/label}} : {{textarea name='content' rows='5' cols='40'/}}<br/>

Теперь шаблон template/news/form_errors.phtml следующего содержания:

    {{form:errors to='$fields_errors'/}}
    {{list using='$fields_errors' as="$error"}}
      <div class="message_error">
        <b class='title'>This fields contained errors</b>
        <ol>
          {{list:item}}
            <li><span style="color:red;">{$error.message}</span></li>
          {{/list:item}}
        </ol>
      </div>
    {{/list}}

Теперь эти шаблоны подключаем в наши template/news/create.phtml и template/news/edit.phtml:

    <html>
    <head>
      <title>Limb3 tutorial</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body bgcolor="#FFFFFF" text="#000000" >
 
    <h1>Create news</h1>
 
    {{form id='news_form' name='news_form' method='post'}}
 
    {{include file="news/form_errors.phtml" /}}
 
    {{include file="news/form_fields.phtml" /}}
 
    {{input type='submit' value='Create'/}}
    {{/form}}
 
    </body>
    </html>

и

    <html>
    <head>
      <title>Limb3 tutorial</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body bgcolor="#FFFFFF" text="#000000" >
 
    <h1>Edit news</h1>
 
    {{form id='news_form' name='news_form' method='post'}}
 
    {{include file="news/form_errors.phtml" /}}
 
    {{include file="news/form_fields.phtml" /}}
 
    {{input type='submit' value='Edit'/}}
    {{/form}}
 
    </body>
    </html>

Обратите внимание на использование тега [{{include}}](../../../../macro/docs/ru/macro/tags/core_tags/include_tag.md). Атрибут **file** указывает на относительное имя шаблона (относительно папки template), который нужно включить в шаблон вместо тега.

Что ж, уже лучше, но можно сделать еще компактнее.

## Выделение общего базового шаблона page.phtml
Код в шаблонах create.phtml и edit.phtml до формы и после нее повторяется дважды. В других шаблонных системах мы сделали бы еще 2 шаблона: header.phtml и footer.phtml, но в MACRO есть средства намного лучше. Подход header/footer имеет большой недостаток в том, что разбивает базовый шаблон на 2 составляющие, которые усложняют общее понимание шаблона в целом. При помощи MACRO этот недостаток можно обойти.

Воспользуемся тегом [{{wrap}}](../../../../macro/docs/ru/macro/tags/core_tags/wrap_tag.md), который позволяет вставлять содержимое тега внуть другого шаблона, то есть как бы обворачивать один шаблон другим.

Но для начала выделим шаблон template/page.phtml следующего содержимого:

    <html>
    <head>
      <title>Limb3 tutorial</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body bgcolor="#FFFFFF" text="#000000" >
      {{slot id="page_placeholder"/}}
    </body>
    </html>

Обратите внимание на тег [{{slot}}](../../../../macro/docs/ru/macro/tags/core_tags/slot_tag.md) — при помощи этого тега мы декларируем место, в которое может быть вставлен другой шаблон. Теперь изменим наши шаблоны create.phtml и edit.phtml следующим образом:

    {{wrap with="page.phtml" into="page_placeholder"}}
 
    <h1>Create news</h1>
 
    {{form id='news_form' name='news_form' method='post'}}
 
    {{include file="news/form_errors.phtml" /}}
 
    {{include file="news/form_fields.phtml" /}}
 
    {{input type='submit' value='Create'/}}
    {{/form}}
 
    {{/wrap}}

и

    {{wrap with="page.phtml" into="page_placeholder"}}
 
    <h1>Edit news</h1>
    {{form id='news_form' name='news_form' method='post'}}
 
    {{include file="news/form_errors.phtml" /}}
 
    {{include file="news/form_fields.phtml" /}}
 
    {{input type='submit' value='Edit'/}}
    {{/form}}
    {{/wrap}}

Тег [{{wrap}}](../../../../macro/docs/ru/macro/tags/core_tags/wrap_tag.md) имеет атрибуты **with**, указывающий на относительное имя файла, в какой шаблон делать вставку, и **into**, который указывает, в какое место этого шаблона нужно её делать. Тег [{{wrap}}](../../../../macro/docs/ru/macro/tags/core_tags/wrap_tag.md) имеет обязательный закрывающий тег, который как бы отмечает зону, которая должна быть обвернута в другой шаблон.

На самом деле тег [{{wrap}}](../../../../macro/docs/ru/macro/tags/core_tags/wrap_tag.md) дает очень широкий простор для разбиения шаблонов, однако эти возможности и принципы их использования выходят за рамки данного примера.

Обвернуть шаблон news/display.phtml с помощью [{{wrap}}](../../../../macro/docs/ru/macro/tags/core_tags/wrap_tag.md) мы оставляем вам возможность самостоятельно.

## Добавление постраничного вывода
Список новостей может быть слишком большим для отображения на одной странице. В этом случае обычно применяют разбиение списка на страницы (пейджинация). При помощи MACRO сделать разбиение на страницы проще простого.

Создадим шаблон template/pager.phtml следующего содержимого:

    {{paginate iterator='$this->news' pager='pager'/}}
 
    {{pager id="pager" items="3"}}
 
    total items : {$total_items}. shown: from <b>{$begin_item_number}</b> to <b>{$end_item_number}</b>
 
    {{pager:first}}<a href="{$href}">first</a>{{/pager:first}}
 
    {{pager:list}}
      {{pager:current}}<b><a href="{$href}">{$number}</a></b>{{/pager:current}}
      {{pager:number}}<a href="{$href}">{$number}</a>{{/pager:number}}
      {{pager:separator}}-{{/pager:separator}}
    {{/pager:list}}
 
 
    {{pager:last}}<a href="{$href}">last</a>{{/pager:last}}
 
 
    {{/pager}}

Центральный тег при постраничном разбиении — [{{paginate/}}](../../../../macro/docs/ru/macro/tags/pager_tags/paginate_tag.md), собственно он и разбивает список на страницы. Он содержит атрибут **iterator**, который указывает на переменную, содержащую список для разбиения на страницы, а так же атрибут **pager**, указывающий на ID тега [{{pager}}](../../../../macro/docs/ru/macro/tags/pager_tags/pager_tag.md), который будет отображать постраничную навигацию. Тег [{{pager}}](../../../../macro/docs/ru/macro/tags/pager_tags/pager_tag.md) содержит **items**, указывающий какое количество элементов списка выводить на одной странице. Остальными тегами выводятся ссылки на первую (тег [{{pager::first}}](../../../../macro/docs/ru/macro/tags/pager_tags/pager_frontier_tag.md)), предыдущую (тег [{{pager:prev}}](../../../../macro/docs/ru/macro/tags/pager_tags/pager_frontier_tag.md)), текущую (тег [{{pager:current}}](../../../../macro/docs/ru/macro/tags/pager_tags/pager_current_tag.md)), следующую (тег [{{pager:next}}](../../../../macro/docs/ru/macro/tags/pager_tags/pager_frontier_tag.md)) и последнюю (тег [{{pager:last}}](../../../../macro/docs/ru/macro/tags/pager_tags/pager_frontier_tag.md)) страницы, а также на некоторое количество страниц в середине (тег [{{pager:number}}](../../../../macro/docs/ru/macro/tags/pager_tags/pager_number_tag.md)).

Также при помощи выражений {$total_items}, {$begin_item_number} и {$end_item_number}, которые по-умолчанию поддерживаются внутри тега [{{pager}}](../../../../macro/docs/ru/macro/tags/pager_tags/pager_tag.md) мы выводим общее количество элементов в списке, а также номера элементов, с которого начинается текущая страница и которым заканчивается текущая страница.

Теперь необходимо этот шаблон подключить на странице отображения списка новостей и связать список новостей с пейджером. Вот новый вид шаблона template/news/display.phtml (вы ведь использовали здесь тег [{{wrap}}](../../../../macro/docs/ru/macro/tags/core_tags/wrap_tag.md), как и в шаблонах форм?):

    {{wrap with="page.phtml" into="page_placeholder"}}
 
    <h1>Newsline</h1>
    <a href='{{route_url params="action:create"/}}'>Create news</a>
 
    <p/>
 
    {{include file='pager.phtml' /}}
 
 
    {{list using="$#news" as="$item"}}
      <table border="1">
      <tr>
        <th>ID</th>
        <th>Date</th>
        <th>Title</th>
        <th>Actions</th>
      </tr>
      {{list:item}}
      <tr>
        <td>{$item.id}</td>
        <td>{$item.date}</td>
        <td>{$item.title}</td>
        <td>
          <a href='{{route_url params="action:edit,id:{$item.id}"/}}'>Edit</a>&nbsp;&nbsp;
          <a href='{{route_url params="action:delete,id:{$item.id}"/}}'>Delete</a>
        </td>
      </tr>
      <tr>
        <td colspan='4'>
          {$item.annotation}
        </td>
      </tr>
      {{/list:item}}
      </table>
    {{/list}}
 
    {{/wrap}}

Если вы все сделали правильно, тогда у вас должно получиться нечто подобное этому:
![Alt-main_page](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:basic:main_page.png)

## Что дальше?
Далее: [Шаг 5. Финальные штрихи (сортировка, отображение новости целиком, пять свежих новостей на главной странице)](./step5.md).
