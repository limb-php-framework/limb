# Тег {{js_checkbox}}
## Описание
Вставляет DHTML checkbox и скрытое поле <input type='hidden'/>. Именно значение этого hidden поля отсылается при отправке формы. В результате получаем checkbox, который всегда возвращает значение: 0 , если флаг снят и **value**, если флаг установлен.

## Синтаксис

    {{js_checkbox id='id' name='name' [value='some_value'] [checked_value='some_checked_value'] [checked='checked']/}}

## Область применения
Обычно внутри [тега {{form}}](./form_tag.md).

## Атрибуты
Любые атрибуты, которые относятся к обычным тегам [{{input}}](./input_tag.md) (см. также [lmbMacroFormElementTag](./lmb_macro_form_element_tag.md)).

## Содержимое
Нет.

## Пример использования

    {{form name='my_form'}}
      <label for='is_special'>Mark as special:</label>  {{js_checkbox name='is_special' id='is_special'/}}
    {{/form}}
