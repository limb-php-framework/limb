# Теги pager:prev, pager:first, pager:next, pager:last
Позволяют вывести ссылки на предыдущую **{{pager:prev}}**, первую **{{pager:first}}**, следующую **{{pager:next}}** и последнюю страницы **{{pager:last}}** списка страниц.

Каждый из этих тегов вставляет переменную **$href** в шаблон с путем до соответствующей страницы, например:

    {{pager:first}}<a href='{$href}'>First page</a>{{/pager:first}}
    {{pager:last}}<a href='{$href}'>Last page</a>{{/pager:last}}

## Синтаксис

    {{pager:first}}
    __some___content__
    {{/pager:first}}
    {{pager:prev}}
    __some___content__
    {{/pager:prev}}
    {{pager:next}}
    __some___content__
    {{/pager:next}}
    {{pager:last}}
    __some___content__
    {{/pager:last}}

## Область применения
Должены быть внутри [тега {{pager}}](./pager_tag.md).

## Атрибуты
Нет.

## Содержимое
Оформление ссылки, где используется выражение {$href}.

## Пример использования
см. пример к [тегу {{pager}}](./pager_tag.md).
