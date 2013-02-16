# Тег {{form}}
## Описание
Теги группы **{{form}}** удобны, если вам необходимо работать с активной формой, то есть проверять поля формы, выводить ошибки валидации, сохранять значения в полях после отправки формы и повторной ее отображении - MACRO содержит широкий набор средств, превращающий эти теги <form>, <input> и т.д. в полноценные активный компоненты.

см. раздел [«Теги форм с активными компонентами»](../../form_tags.md), который подробно объясняет этот момент.

## Синтаксис

    {{form id='id' [..любые атрибуты html-тега <form>] [from='$some_var']}}
      ...some form controls...
    {{/form}}

## Область применения
В любом месте MACRO шаблона.

## Атрибуты
* **id** — идентификатор формы.
* **from** — указывает на переменную, которая будет использоваться в качестве контейнера данных для формы

Плюс любые атрибуты html-тега <form>

Если вы не указали явно параметр **name**, то оно будет сгенерировано со значением параметра **id**.

## Содержимое
Любое.

## Передача данных и ошибок валидации в форму через спец. переменные шаблона

На этапе выполнения шаблона тегу **{{form}}** соответствует так называемый runtime компонент. Этот компонент может автоматически использовать в качестве контейнера данных и списка ошибок валидации переменные шаблона, названные особым образом:

* form_[form_runtime_id]_datasource
* form_[form_runtime_id]_error_list

Приведем небольшой пример:

    <? $this->form_my_form_datasource = array('value' => 1111); ?>
    <? $this->form_my_form_error_list = array(array('message' => 'Some error')); ?> 
 
    {{form name="my_form"}}
     <? $ds = $this->form_my_form->getDatasource();
      echo $ds["value"]; ?>
      <br/>
     <? $error_list = $this->form_my_form->getErrorList();
      echo $error_list[0]["message"]; ?>
    {{/form}}

На выходе получим:

    <form name=«my_form»>1111<br/>Some error</form>

Именно эти переменные ставит класс lmbMacroView, который можно найти в пакете VIEW.

## Пример использования
см. также пример для [lmbMacroFormElementTag](./lmb_macro_form_element_tag.md).

    {{form id='search_form' name='search_form' action='/search' method='get' }}
      <tr>
        <td >
         Search
        </td>
        <td>
         {{input type="text" id="query" name="query" class='input' size='40'/}}
        </td>
      </tr>
      <tr>
        <td colspan='2'>
         <input type="submit" value='Search'/>
        </td>
      </tr>
      <input type="hidden" name="action" value='create'/>
    {{/form}}
