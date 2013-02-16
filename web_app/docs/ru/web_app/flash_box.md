# Передача произвольных сообщений пользователям. Flashbox
Часто позникает передать пользовалю сообщение, например, об успешно выполненной операции или о возникновении какой-либо ошибки. Для этих целей есть в Limb3 такое понятие, как flashbox, идея которого в общем-то была заимствована из Ruby-on-Rails.

Flashbox хранит сообщения в сесии до момента их отображения. При первом отображении сообщения из flashbox-а удаляются.

## Добавление сообщений во flashbox
Flashbox — это контейнер обычных сообщений 2 видов:

* обычные (уведомления)
* критические (ошибки)

Объект flashbox-а можно получить из toolkit-а при помощи метода **getFlashbox()**.

Для добавления сообщений во flashbox есть методы:

* **addError($error)**
* **addMessage($message)**

Тулкит также содержит методы для быстрого добавления сообщений во flashbox:

* **flashMessage($message)**
* **flashError($message)**

Точно такие же методы есть в классе lmbController:

    class PollController extends lmbController
    {
      function doCreate()
      {
        $item = new Poll();
        $this->useForm('poll_form');
        $this->setFormDatasource($item);
 
        if($this->request->hasPost())
        {
          $item->import($this->request->export());
          if($item->trySave($this->error_list))
            $this->flashMessage("Опрос {$item->getSignature()} создан");
        }
      }
    }

## Вывод сообщений
Для вывода сообщений в MACRO-шаблонах используется специальный тег {{flashbox}}, который передает список сообщений (массив) в переменную $flashbox. Если нужно использовать другую переменную для передачи списка, то можно воспользоваться специальным атрибутом {{flashbox to="$variable"}}.

    {{flashbox}}
    {{list using="$flashbox"}}
      {{list:item}}
        {$item.message}
      {{/list:item}}
    {{/list}}
    {{/flashbox}}

Для вывода сообщений в WACT-шаблонах используется специальный WACT тег <flash_box>, который передает в тег <list:list> список сообщений:

    <flash_box target="flash_box"/>
    <list:list id="flash_box">
      <list:ITEM>
        <core:optional for='is_error'>Это ошибка: {$message}</core:optional>
        <core:optional for='is_message'>Это уведомление: {$message}</core:optional>
        <br/>
      </list:ITEM>
    </list:list>
