# Тег {{input}}
## Описание
Расширенная форма html-тега <input>. Является потомком [lmb_macro_form_element_tag](./lmb_macro_form_element_tag.md).

## Синтаксис

    {{input id='id' type='text|checkbox|hidden...' [...любые html-параметры]/}}

## Атрибуты

* **type** — тип поля (text, hidden, image, button, password, submit, reset, file, checkbox, radio)
* Наследованные атрибуты [lmb_macro_form_element_tag](./lmb_macro_form_element_tag.md)

## Содержимое
Нет.

## Widget (runtime компонент)
В откомпилированном виде каждому **{{input}}** тегу соответствует объект определенного класса или так называемый runtime компонент. То, какой компонент будет выбран, зависит от атрибута **type**.

Вот таблица соответствия:

Тип | Класс компонента
----|-----------------
text, hidden, image, button |	lmbMacroInputWidget
password, submit, reset, file	| lmbMacroFormElementWidget
checkbox, radio	| lmbMacroCheckableInputWidget

* lmbMacroInputWidget — рендерит для <input> тега в любом случае атрибут **value**.
* lmbMacroCheckableInputWidget — трактует value как значение, с которым нужно знавнить так называемое **checked_value**. Набор правил, по которым checkbox или radio становится checked см. ниже.

## Как работают checkbox-ы и radio
Если для обычных input-ов атрибут value определяет значение поля, то для type='checkbox' и type='radio' атрибут **value** имеет другое значение: **value** используется как значение, с которым нужно сравнить некое **checked_value**.

Этот **checked_value** определяется следующим образом:

* Сначала проверяется наличие атрибута **checked_value**
* Если этого атрибута нет, тогда берется значение в соответствие с runtime_id из контейнера данных родительского тега **{{form}}** (runtime_id равно id или name)

Потом на основе **value** и **checked_value** вычисляется, нужно ли выводить (рендерить) для тега <input> атрибут checked. Атрибут **checked** выводится если:

checked_value | value	| пример
--------------|-------|-------
массив | есть в массиве	| {{input type="checkbox" checked_value="$#obj->getTypesArray()" value="foo"}}
скаляр | равен checked_value | {{input type="checkbox" checked_value="foo" value="foo"}}
скаляр | отсутствует | {{input type="checkbox" checked_value="foo"}}
0 или false	| 0	| {{input type="checkbox" checked_value="0" value="0"}}

или если он указан явно.

## Пример использования
### text, hidden

    <label for='name'>Имя и фамилия:</label>
    {{input type="text" name="name" id="name" title="Имя и фамилия"/}}

### checkbox

    <? $bar = 1; ?>
    {{input type="checkbox" id="test" name="my_input" value="1" checked_value="{$bar}" /}}

Получим:

    <input type="checkbox" id="test" name="my_input" value="1" checked="true"/>

### radio

    Выберите тип пользователя:
    <label for='type_user'>{{input type="radio" name="member_type" id="type_user" value='1' title='Фотограф' checked='true'/}}Фотограф</label>
    <label for='type_model'>{{input type="radio" name="member_type" id="type_model" value='2' title='Модель'/}}Модель</label>
