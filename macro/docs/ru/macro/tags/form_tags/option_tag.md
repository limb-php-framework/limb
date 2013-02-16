# Тег {{option}}
## Описание

Расширенная форма html тега <option>. **{{option}}** тег класса lmbMacroPassiveTag, это значит, что он не генерит самостоятельно код в компилируемый шаблон. Цель **{{option}}** тега — предоставить возможность тегу [{{select}}](./select_tag.md) найти дополнительные опции, заданные в шаблоне явно.

## Синтаксис

    {{option value='option_value' [prepend='true'] [selected='true']}}Option title{{/option}}

## Содержимое
Название опции.

## Атрибуты
* **value** — значение
* **prepend** — указывает, что данная опция должна встать в начало списка опций, который генерирует **{{select}}** тег. Это актуально, когда **{{select}}** тег заполняется опциями при помощи атрибута **options**.
* **selected** — указывает, что данная опция должна быть выбранной по-умолчанию, если родительский **{{select}}** тег не может определить, какая опция должна быть помечена как выбранная исходя из атрибута **value** или по контейнеру данных в родительском теге **{{form}}**.

## Пример использования

    <? $options = array(2 => 'test2', 3 => 'test3'); ?>
 
    {{select name="my_select" options="$options"}}
    {{option value="1" prepend="true"}}test1{{/option}}
    {{option value="4" selected="true"}}test4{{/option}}
    {{/select}}

На выходе получим:

    <select name="my_select">
    <option value="1">test1</option>
    <option value="2">test2</option>
    <option value="3">test3</option>
    <option value="4" selected="true">test4</option>
    </select>
