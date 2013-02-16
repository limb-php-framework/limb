# Тег {{allowed}}
## Описание
Используется для контроля вывода элементов, основанного на правах [ACL](../../../../../../acl/docs/ru/acl.md).

##Область использования

В любом месте MACRO шаблона.

## Синтаксис

    {{allowed role='<role>' resource='<resource>' privelege='privilege'}}protected content{{/allowed}}

## Атрибуты

* **role** (*string|lmbRoleProviderInterface*) — имя роли, либо [объект — носитель роли](../../../../../../acl/docs/ru/acl.md)
* **resource** (*string|lmbResourceProviderInterface*) — имя ресурса, либо [объект — носитель ресурса](../../../../../../acl/docs/ru/acl.md)
* **privilege** (*string*) — имя привилегии

## Содержимое
Любое.

## Пример использования
Для [схемы]((../../../../../../acl/docs/ru/acl.md) из документации на пакет ACL:

    {{allowed role="member" resource="article" privelege="create" }}<a href="/article/create">create</a>{{/allowed}}
    {{allowed role="$member" resource="$article" privelege="edit" }}<a href="/article/edit/3">edit</a>{{/allowed}}
