# Тег {{template}}
## Описание
Определяет кусок шаблона, который может быть неоднократно переиспользован при помощи тега [{{apply}}](./apply_tag.md). Комбинация {{template}} и {{apply}} позволяет делать аналог {{include}}, но только в рамках одного шаблона. Содержимое {{template}} без использования {{apply}} не выводит никакой код.

Механизм работы этой связки такой: при компиляции содержимое {{template}} компилируется в отдельный метод. {{apply}} — это по сути вызов этого метода.

## Синтаксис

    {{template name='some_id'}}
     [...Some Content...]
    {{/template}}

## Область применения
В любом месте MACRO шаблона.

## Атрибуты
* **name** — идентификатор.

## Содержимое
Любое.

## Пример использования

    {{template name="photo_tpl"}}
      <span class="date">{$item.ctime|date:"d.m.Y"}</span>
      <a href="/photo/item/{$item.id}" class="img"><img alt="{$item.title}" src="{$item.icon_file_url}"></a>
      <a href="/photo/item/{$item.id}" class="title">{$item.title}</a>
      <a href="#" class="author">{$item.member.nick}</a>
      <a href="{$item.thumbnail_file_url}" title="{$item.title}" class='preview'>предварительный просмотр</a>
    {{/template}}
 
    <h2>Лучшие фото рубрики {$#category.title}</h2>
    {{list using="$#best_photos" as="$photo"}}
    <ul id='best_photos_list'>
      {{list:item}}
       <li>{{apply template="photo_tpl" item="$photo"/}}</li>
      {{/list:item}}
    </ul>
    {{/list}}
 
    <h2>Все фото рубрики {$#category.title}</h2>
    {{list using="$#photos" as="$photo"}}
    <ul id='photos_list'>
      {{list:item}}
       <li>{{apply template="photo_tpl" item="$photo"/}}</li>
      {{/list:item}}
    </ul>
    {{/list}}

### Дополнительные примеры
Большое количество примеров с комментариями можно найти в разделе [«Композиция шаблонов»](../../template_composition.md).
