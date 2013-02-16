# Контейнеры данных Limb3: интерфейсы и базовые классы
## Итераторы
Итераторы — стандартная форма работы со списковыми данными. Грубо говоря, под итератором можно понимать просто массив или массив массивов (очень условно).

Итератор обычно содержит следующие методы:

* rewind() — переводит внутренний курсор итератора на начальное положение (перемотка на начало).
* valid() — возвращает true, если курсор не вышел за границы итератора.
* current() — возвращает текущее значение итератора, на которое указывает курсор.
* next() — переводит внутренний курсор итератора на следующее положение.

Обычная форма работы с итератором выглядит так:

    for($iterator->rewind(); $iterator->valid(); $iterator->next())
    {
      $item = $iterator->current();
      [..do something with $item..]
    }

или в сокращенной форме:

    foreach($iterator as $item)
    {
      [..do something with $item..]
    }

В PHP5 интерфейсы [Iterator](http://www.php.net/~helly/php/ext/spl/interfaceIterator.html), [Traversable] (http://www.php.net/~helly/php/ext/spl/interfaceTraversable.html), [Countable](http://www.php.net/~helly/php/ext/spl/interfaceCountable.html) и [ArrayAccess](http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html) являются уже встроенными и входят в [библиотеку SPL (StandardPHPLibrary)](http://www.php.net/~helly/php/ext/spl/). Есть [хорошая статья-введение в SPL на sitepoint.com от Harry Fuecks](http://www.sitepoint.com/php5-standard-library/).

Однако базовых интерфейсов нам показалось мало, и мы расширили Iterator до интерфейса [lmbCollectionInterface](./lmb_collection_interface.md):

Пакет CORE содержит следующий набор интфейсов и классов для работы с итераторами:

Класс/Интерфейс | Назначение
----------------|-----------
[lmb_collection_interface](./lmb_collection_interface.md) | Интерфейс для работы с итераторами, который применяется в Limb
[lmb_collection](./lmb_collection.md) |	Класс, реализующий lmbCollectionInterface. По-сути объектная форма массива ассоциативных массивов
[lmb_collection_decorator](./lmb_collection_decorator.md)	| Базовый декоратор на итератор с интерфейсом lmbCollectionInterface

## Единичные (несписковые) контейнеры данных
Единичные (несписковые) контейнеры данных можно представлять как обычный ассоциативный массив в объектной форме. На самом деле все не так просто…

SPL содержит интерфейс [ArrayAccess](http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html), который позволяет работать с классами, как с обычными массивами. Однако из-за того, что в Limb3 работа преимущественно ведется с объектами, мы решили, что было неплохо иметь возможность работать наоборот — с массивами как с объектами. Результатом этого появился интерфейс [lmbSetInterface](./lmb_set_interface.md) и класс [lmbSet](./lmb_set.md).

Важным классом также является [lmbObject](./lmb_object.md), который используется в качестве базового для объектов модели. lmbObject используется в пакете ACTIVE_RECORD в качестве родительского lmbActiveRecord. lmbObject вносит достаточно много «магии» в ваш код, поэтому его использование может как увеличить гибкость вашего кода и его наглядность, так и отнять у вас много времени на отладку, если этой «магией» неправильно пользоваться.

Класс/Интерфейс | Назначение
[lmbSetInterface](./lmb_set_interface.md)	| Интерфейс для работы с единичными контейнерами данных
[lmbSet](./lmb_set.md) |	Класс, реализующий lmbSetInterface. По-сути объектная форма ассоциативного массива
[lmbObject](./lmb_object.md) | Базовый класс, для объектов модели в Limb3. lmbObject поддерживает магические getter()-ы и setter()ы и реализует lmbSetInterface.

## Для чего все это?
* Итераторы, поддерживающие постраничный вывод оказываются очень полезны в шаблонах.
* Итераторы легко декорировать и декорирование полностью прозначно для клиентов. С массивами такой фокус не проходит — многое приходится делать вручную.
* Единая форма работы с массивами и объектами через интерфейс lmbSetInterface — оказалась удобной в шаблонах, когда вместо

        if(isset($item['some_property'])) 
         echo $item['some_property'];

можно просто написать

    echo $item->get('some_property').

и пусть $item сам разбирается, есть у него такая some_property или нет.

А при использовании lmbObject-а в качестве $item из вышестоящего примера с методом getSomeProperty() будет вызван именно этот метод.
