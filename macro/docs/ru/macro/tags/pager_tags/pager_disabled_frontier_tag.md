# Теги {{pager:prev:disabled}}, {{pager:first:disabled}}, {{pager:next:disabled}}, {{pager:last:disabled}}
## Описание
Позволяет вывести текст, если вывод соответствующего этому обычного тега невозможен для текущей страницы. Например, если мы находимся на первой странице, тогда тег [{{pager:first}}](./pager_frontier_tag.md) ничего не отобразит, а тег **{{pager:first:disabled}}** выведет свое содержимое.

## Синтаксис

    {{pager:first:disabled}}
    __some___content__
    {{/pager:first:disabled}}
    {{pager:prev:disabled}}
    __some___content__
    {{/pager:prev:disabled}}
    {{pager:next:disabled}}
    __some___content__
    {{/pager:next:disabled}}
    {{pager:last:disabled}}
    __some___content__
    {{/pager:last:disabled}}

## Область применения
Должены быть внутри [тега {{pager}}](./pager_tag.md).

## Атрибуты
Нет.

## Содержимое
Любое.

## Пример использования

    {{pager id="test_pager" items="10" total_items="20"}}
    {{pager:first:disabled}}First disabled|{{/pager:first:disabled}}
    {{pager:prev:disabled}}Prev disabled|{{/pager:prev:disabled}}
    {{pager:next:disabled}}Next disabled|{{/pager:next:disabled}}
    {{pager:last:disabled}}Last disabled{{/pager:last:disabled}}
    {{/pager}}

Получим:

    First disabled|Prev disabled|

«Next disabled» и «Last disabled» не будут выведены, так как ссылки на них можно вывести.
