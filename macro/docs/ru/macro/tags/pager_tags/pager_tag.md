# Тег {{pager}}
## Описание
{{pager}} позволяет выводить так называемый **pager**, то есть список страниц со ссылками.

MACRO pager поддерживает 2 способа вывода большого количества ссылок:

1. режим **elipses**, когда выводится только несколько страниц вокруг текущей и некоторое количество страниц в начале и в конце списка (см. [тег {{pager:elipses}}](./pager_elipses_tag.md)). Список страниц будет выглядеть приблизительно так: 1-2-3…7-8-9…20-21-22. Этот режим предпочтительнее, когда для пользователя по сути не важно попасть на какую-то определенную страницу.
2. режим **sections** (секции), которые можно понимать как страницы страниц. Список страниц будет выглядеть приблизительно так: [1-10][11-20]21-22-23-24-25-26-27-28-29-30[31-40][40-47]

Конечно, можно выводить просто ссылка на первую, предыдущую, следующую и последнуюю страницы, если это вам продходит.

Выделяют следующие элементы pager-а:

* Ссылка на первую страницу. Тег {{pager:first}}
* Ссылка на предыдущую страницу. Тег {{pager:prev}}
* Ссылка на следующую страницу. Тег {{pager:next}}
* Ссылка на последнуюю страницу. Тег {{pager:last}} (cм. [Теги {{pager:prev}}, {{pager:first}}, {{pager:next}}, {{pager:last}}](./pager_frontier_tag.md)
* Ссылка на страницу. Тег [{{pager:number}}](./pager_number_tag.md)
* Ссылка на текущую страницу. Тег [{{pager:current}}](./pager_current_tag.md)
* Ссылка на секцию. Тег [{{pager:section}}](./pager_section_tag.md)
* **$begin_item_number** — переменая с номером начального элемента , выводимого на текущей странице.
* **$end_item_number** — переменная с номером конечного элемента, выводимого на текущей странице.
* **$total_items** — общее количество элементов.
* **$total_pages** — Общее количество страниц.
* **$items_per_page** — количество элементов на одной странице

Вывод pager-а достаточно гибко настраивается.

см. также разделы

* [«Постраничный вывод данных в WACT-шаблонах»](../../pagination.md)
* [тег {{paginate}}](./paginate_tag.md) — тег, позволяющий связывать Limb3-итераторы c pager-ом в MACRO-шаблоне.

## Синтаксис

    {{pager id='pager_id' items='items_per_page' total_items='total_items' [pages_per_section='pages_per_section'] [pages_in_middle='pages_in_middle'] [pages_in_sides='pages_in_sides']/>
    __some_chil_tags_with_decorating_content__
    {{/pager}}

## Область применения
В любом месте MACRO шаблона.

## Атрибуты

* **id** — идентификатор тега.
* **items** [опционально] — позволяет указать, сколько записей из итератора должно выводиться на одной странице.
* **total_items** [опционально] — позволяет указать, сколько всего элементов в том списке, который разбивается на страницы.
* **pages_per_section** [опционально] — позволяет указать, сколько страниц должно находиться в одной секции. Атрибут учитывается, если внутри нет [тега {{pager:elipses}}](./pager_elipses_tag.md).
* **pages_in_middle** [опционально] — позволяет указать количество страниц в центре pager-а, включая текущую страницу. Атрибут учитывается, если используется [тег {{pager:elipses}}](./pager_elipses_tag.md).
* **pages_in_sides** [опционально] — позволяет указать количество страниц по бокам pager-а. Атрибут учитывается, если используется [тег {{pager:elipses}}](./pager_elipses_tag.md).

## Содержимое
Дочерние теги, плюс элементы оформления.

## Как осуществляется пейджинация
Кратко как осуществляется так называемая пейджинация с участием {{pager}}-а:

1. Есть итератор, который поддерживает интерфейс lmbCollection (пакет CORE)
2. тег {{paginate}}:
  * передает в {{pager}} количество элементов все в списке
  * {{pager}} сам вычисляет на основе переменной $_GET[pager_id] номер текущей страницы.
  * далее {{paginate}} забирает из {{pager}}-а количество элементов на одной страницы и номер элемент, с которого должна начаться текущая страница и вызывает метод paginate() с этими параметрами.

## Пример использования
### Pager с секциями

    {{pager id="pager" items="5" pages_per_section="5"}}
 
    total items : {$total_items}. shown: from <b>{$begin_item_number}</b> to <b>{$end_item_number}</b>
 
    {{pager:first}}<a href="{$href}">first</a>{{/pager:first}}
    {{pager:prev}}<a href="{$href}">first</a>{{/pager:prev}}
 
    {{pager:list}}
    {{pager:section}}<a href="{$href}">[{$section_begin_page}..{$section_end_page}]</a>{{/pager:section}}
    {{pager:current}}<b><a href="{$href}">{$number}</a></b>{{/pager:current}}
    {{pager:number}}<a href="{$href}">{$number}</a>{{/pager:number}}
    {{pager:separator}}-{{/pager:separator}}
    {{/pager:list}}
 
    {{pager:next}}<a href="{$href}">next</a>{{/pager:next}}
    {{pager:last}}<a href="{$href}">last</a>{{/pager:last}}
 
    {{/pager}}

Выведет приблизительно вот такой pager:

    Total items : 174. Shown: from 121 to 130.
    First Prev [1..5][6..10]11-12-13-14-15[16..17] Next Last

### Pager с elipses

    {{pager:navigator id="pager" items="5" pages_in_middle="5" pages_in_sides="3"}}
 
    {{pager:list}}
    {{pager:current}}<b><a href="{$href}">{$number}</a></b>{{/pager:current}}
    {{pager:number}}<a href="{$href}">{$number}</a>{{/pager:number}}
    {{pager:elipses}}...{{/pager:elipses}}
    {{pager:separator}}-{{/pager:separator}}
    {{/pager:list}}
 
    {{/pager:navigator}} 

Выведет приблизительно вот такой pager:

    1-2-3...6-7-8-9-10...15-16-17
