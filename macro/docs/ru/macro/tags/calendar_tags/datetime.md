# Тег {{datetime}}
## Описание
Выводит JavaScript-календарь.

Является потомком [lmbMacroFormElementTag](../form_tags/lmb_macro_form_element_tag.md), и наследует его атрибуты.

## Синтаксис

    {{datetime id="<id>" name="<date_field_name>"/}}

## Область применения
Внутри формы.

## Атрибуты

См. атрибуты [lmbMacroFormElementTag](../form_tags/lmb_macro_form_element_tag.md).

* **format** — выходной формат, в котором придет значение
* **striped** — ???
* **lang** — язык (названия месяцев)

## Содержимое
Нет.

## Пример использования

    {{datetime id="date" name="date" title="Дата" lang="ru" format="%d-%m-%Y" disabled="disabled"/}}
