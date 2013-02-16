# Постраничный вывод данных в MACRO-шаблонах
Чаще всего списки слишком длинные, чтобы их показывать на одной странице. Для решения этой проблемы применяют разбиение списков на страницы. Этот список страниц обычно называют пейджером (pager).

Задача по разбиению длинных списков на страницы состоит из 2 под-задач:

* Вывод pager-а.
* Лимитирование списка, так что бы он содержал данные только для текущей страницы. В случае с выборкой из базы данных — применение limit, offset.

## Вывод pager-а
Для вывода pager-а используется группа тегов PagerTags.

Приведем пример:

    {{pager id="my_pager" items="10" total_items="$#total_items_in_list"}}
 
    Показаны новости: с <b>{$begin_item_number}</b> по <b>{$end_item_number}</b>
 
    {{pager:first}}<a href="{$href}">Первая страница </a>{{/pager:first}}
 
    {{pager:list}}
 
    {{pager:current}}<b><a href="{$href}">{$number}</a></b>{{/pager:current}}
    {{pager:number}}<a href="{$href}">{$number}</a>{{/limb:pager:number}}
 
    {{/pager:list}}
 
    {{pager:last}}<a href="{$href}">Последняя страница</a>{{/pager:last}}
 
    Всего новостей: <b>{$total_items}</b>
    {{/pager}}

Центральный тег при постраничном разбиении — [тег {{pager}}](./tags/pager_tags/pager_tag.md), который ограничивает пейджер. Он содержит атрибут **items**, который указывает, какое количество элементов списка выводить на одной странице. Атрибут **total_items** указает на общее количество элементов в списке, разбиваемой на страницы. Номер текущей страницы pager узнает из $_GET автоматически.

[Тег {{pager:list}}](./tags/pager_tags/pager_list_tag.md) — огранизует вывод номеров страниц pager-а.

Остальные теги:

* [Теги {{pager:prev}}, {{pager:first}}, {{pager:next}}, {{pager:last}}](./tags/pager_tags/pager_frontier_tag.md) — выводят ссылку на предыдущую, первую, следующую и последнуюю страницы списка страниц;
* [Тег {{pager:current}}](./tags/pager_tags/pager_current_tag.md) — на текущую;
* [Тег {{pager:number}}](./tags/pager_tags/pager_number_tag.md) — на страницу с определенным номером.

Внутри каждого тега доступна переменная **$href**, содержащая ссылку на нужную страницу. Ссылка в $href содержит все параметры, которые есть в перменной $_GET, а также дополнительный параметр, указывающий на номер страницы pager-а. Теги {{pager:number}} и {{pager:current}} также содержат переменную **$number** — номер страницы.

Также внутри тега {{pager}} доступны следующие переменные:

* **{$total_items}** — общее количество элементов в списке,
* **{$total_pages}** — общее количество страниц,
* **{$begin_item_number}** — номер элемента, с которого начинается текущая страница,
* **{$end_item_number}** — номер элемента, которым заканчивается текущая страница.

Обычно проекте реализуют один (или несколько) pager-ов, которые обычно подключаются к спискам по мере необходимости при помощи [тега {{include}}](./tags/core_tags/include_tag.md).

## Лимитирование списка
Для того, чтобы {{pager}} знал количество элементов, можно использовать атрибут **total_items**, однако для тех, кто работает и с остальными пакетами Limb3, можно позвользоваться [тегом {{paginate}}](./tags/pager_tags/paginate_tag.md). Оговоримся сразу - данный метод работает только для тех списков, которые реализуют интерфейс lmbCollectionInterface (пакет CORE). Для других случаев в можете легко реализовать свой собственный вариант в виде MACRO-тега.

Допустим у нас есть шаблон:

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

Обратите внимание, что данные в списке должны быть в переменной $this→modules в шаблоне до момента отработки тега {{paginate}}.

## А если страниц очень много?
Если у вас слишком много страниц (ну скажем больше 20), то можно применить 2 варианта:

* Ввести разрывы (elipses) — когда отображаются страницы в начале, в середине и в конце списка страниц, а в остальных местах - пропуски. Это выглядит приблизительно так: 1-2-3…7-8-9-10-11…17-18-19
* Ввести секции (sections) — некоторые блоки страниц, которые показывают только начальную и конечную страницы блока. Только 1 секция активна в текущий момент. Это выглядит приблизительно так: [1-5]6-7-8-9-10[11-15][16-19].

см. также описание [тега {{pager}}](./tags/pager_tags/pager_tag.md).

### Разрывы (elipses)
Для вывода разрывов используется [тег {{pager:elipses}}](./tags/pager_tags/pager_elipses_tag.md), а также атрибуты **pages_in_middle** и **pages_in_sides** тега {{pager}}.

Пример:

    {{pager id="pager" items="5" pages_per_section="5"}}
 
    {{pager:list}}
    {{pager:section}}<a href="{$href}">[{$section_begin_number}..{$section_end_number}]</a>{{/pager:section}}
    {{pager:current}}<b><a href="{$href}">{$number}</a></b>{{/pager:current}}
    {{pager:number}}<a href="{$href}">{$number}</a>{{/pager:number}}
    {{pager:separator}}-{{/pager:separator}}
    {{/pager:list}}
 
    {{/pager}}

Выведет приблительно следующий pager:

    1-2-3...6-7-8-9-10...15-16-17

### Секции (sections)
Для вывода секций используется [тег {{pager:section}}](./tags/pager_tags/pager_section_tag.md), а также атрибут **pages_per_section** тега {{pager}}.

Пример:

    {{pager id="pager" items="5" pages_per_section="5"}}
 
    {{pager:list}}
    {{pager:section}}<a href="{$href}">[{$section_begin_number}..{$section_end_number}]</a>{{/pager:section}}
    {{pager:current}}<b><a href="{$href}">{$number}</a></b>{{/pager:current}}
    {{pager:number}}<a href="{$href}">{$number}</a>{{/pager:number}}
    {{pager:separator}}-{{/pager:separator}}
    {{/pager:list}}
 
    {{/pager}}

Внутри тега {{pager:section}} можно использовать переменные **$section_begin_number** и **$section_end_number**.

Выведет приблизительно следующий pager:

    [1..5][6..10]11-12-13-14-15[16..17]

## Дополнительные примеры
Очень подробный пример использования группы тегов Pager Tags приведен на странице описание [тега {{pager}}](./tags/pager_tags/pager_tag.md).
