# Тег {{textarea}}
## Описание
Расширенная форма html тега <textarea>.

Является потомком [lmb_macro_form_element_tag](./lmb_macro_form_element_tag.md), и наследует его атрибуты.

При выводе значения применяет htmlspecialchars для этого значения.

## Синтаксис

    {{textarea id='id' name='name'[...]/}}

## Содержимое
Нет.

## Атрибуты
См. атрибуты [lmbMacroFormElementTag](./lmb_macro_form_element_tag.md).

## Пример использования

    <? $value = "<< super >>"; ?>
    {{textarea name="my_textarea" value="$#value" /}}

Получим:

    <textarea name="my_textarea">&lt;&lt; super &gt;&gt;</textarea>
