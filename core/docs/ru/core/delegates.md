# Объектные формы call_back вызовов
Пакет CORE представляет базовые средства для реализации паттерна OBSERVER. Примеры использования этих средств см. в пакете ACTIVE_RECORD ([Регистрация слушателей на объекты](../../../../active_record/docs/ru/active_record/enhancement.md)) и в пакете WEB_APP (см. [класс lmbFormCommand](../../../../web_app/docs/ru/web_app/lmb_form_command.md)

В реализации Observer различают 2 роли:

* Сервер (Observable), который генерит сообщения
* Слушатели или подписчики(Observer), которые подписываются на события сервера.

За реализацию этих средств отвечает класс **lmbDelegate**. Класс lmbDelegate — это по сути объектная форма вызова метода какого-либо объекта.

Пример использования класса **lmbDelegate для подписки на события** класса lmbActiveRecord

    $callback = new lmbDelegate($this, 'clearCache');
    lmbActiveRecord :: registerGlobalOnAfterSaveCallback($callback);
    lmbActiveRecord :: registerGlobalOnAfterDestroyCallback($callback);

Здесь мы подписались методом clearCache какого-го объекта на события on_after_save и on_after_destroy класса lmbActiveRecord.

lmbDelegate может принимать различные параметры в конструктор:

    $callback1 = new lmbDelegate($this, 'someMethod'); // метод объекта
    $callback2 = new lmbDelegate('someGlobalFunction'); // глобальная функция
    $callback3 = new lmbDelegate(array($this, 'someMethod')); // callback в виде массива
    $callback3 = new lmbDelegate('MyClass', 'someStaticMethod'); // статический метод класса

Также есть метод lmbDelegate :: **objectify($callback)**, который автоматически распознает, пришел ли ему объект класса lmbDeledate или же один из вариантов, которые можно передать в конструктор.

Подписка на сообщения — это еще не все: необходимо предусмотреть средства **оповещения** слушателей. Для этого в классе lmbDelegate есть методы:

* **invoke()** — вызывает call_user_func_array для данных, переданных в конструктор lmbDelegate, а в качестве параметров вызова - все, что были переданы в invoke.
* **invokeArray($args = array())** — аналог invoke, только в качестве параметров вызова передаются $args.
* **invokeAll($list, $args = array())** — статический метод. Вызывает invokeArray для всех объектов в списке $list
* **invokeChain($list, $args = array())** — статический метод. Вызывает invokeArray для всех объектов в списке $list, однако в отличие от invokeAll цепочка прерывается, как только хоть один из callback-ов ворачивает не-NULL значение

Например:

    if(count($this->_listeners))
      lmbDelegate :: invokeAll($this->_listeners, array($this));

Здесь для каждого случашате из $this→_listeners будет вызван соответствующий callback и в качестве параметра будет передан сам оповещающий объект ($this).
