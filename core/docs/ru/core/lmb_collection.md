# lmbCollection
Класс lmbCollection реализует интерфейс [lmb_collection_interface](./lmb_collection_interface.md). см. также [Контейнеры данных](./data_containers.md).

Класс lmbCollection используется в случах, когда нужно представить массив данных (вернее массив ассоцивных массивов) в виде итератора.

Возьмем самым простой пример. Мы создадим небольшой массив данных, сделаем из него lmbCollection и пройдемся по нему при помощи интерфейса **Iterator**.

    $data = array (
      array ('username' => 'jdoe', 'email' => 'jdoe@hotmail.com'),
      array ('username' => 'rsmith', 'email' => 'rsmith@cure.org'),
      array ('username' => 'nclark', 'email' => 'nclark@yahoo.com'),
    );
 
    $rs = new lmbCollection($data);
 
    $str = '';
    for($rs->rewind(); $rs->valid(); $rs->next())
    {
      $record = $rs->current();
      $str .= 'User: '. $record->get('username') . ' ';
      $str .= 'Email: '. $record->get('email') . "\n";
    }

* **rewind()** — переводит внутренний счетчик в начальное положение
* **valid()** — проверяет не перещел ли внутренний счетчик за положенные границы
* **next()** — переводит внутренний счетчик на следующую позицию
* **current()** — возвращает объект класса [lmb_set](./lmb_set.md), содержащий данные очередного элемента массива.

Так как lmbCollection реализует интерфейс **Traversable**, можно использовать более короткую форму прохождения по итератору через foreach():

    $data = array (...);
    $iterator = new lmbCollection($data);
    foreach($iterator as $item)
     echo $item->get('username') . ' ';  // выведет jdoe rsmith nclark

Так как **lmbCollection реализует SPL интерфейс ArrayAccess** можно написать и так:

    $data = array(...);
    $iterator = new lmbCollection($data); 
    echo sizeof($iterator); // выведет 3
    echo $iterator[1]->get('username') // выведет rsmith

В качестве данных в lmbCollection можно прислать массив объектов класса lmbSet или любых других, которые поддерживают интерфейс lmbSetInterface:

    $data = array (
      new lmbSet(array('username' => 'jdoe', 'email' => 'jdoe@hotmail.com')),
      new lmbSet(array('username' => 'rsmith', 'email' => 'rsmith@cure.org')),
      new lmbSet(array('username' => 'nclark', 'email' => 'nclark@yahoo.com')),
    );
 
    $iterator = new lmbCollection($data);

lmbCollection поддерживает **лимитирование**, например:

    $data = array(...); 
    $iterator = new lmbCollection($data);
    $iterator->paginate(1, 2);
    foreach($iterator as $item)
      echo $item->get('username') . ' ' . $item->get('email') . ' | '; // выведет rsmith rsmith@cure.org | nclark nclark@yahoo.com |

Кроме методов, характерных для интерфейса lmbCollectionInterface поддерживаем след. методы:

* **add($item)** — позволяет добавлять элементы в коллекцию.
* **isEmpty()** — возвращает true, если коллекция пустая
* **toFlatArray()** — возвращает массив массивов. Если данные, которые пришли в lmbCollection содержали объекты, поддерживающие интерфейс lmbSetInterface (как в примере выше), тогда этот метод будет вызывать export() для каждого элемента коллекции.

**Дополнительные примеры** по использованию класса lmbCollection см. **в тестах**:

* limb/core/tests/cases/lmbCollectionTest.class.php
* limb/core/tests/cases/lmbCollectionPaginationTest.class.php
