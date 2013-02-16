# Класс lmbSet
lmbSet — по-сути объектная форма для массива данных. Реализует интерфейсы [lmb_set_interface](./lmb_set_interface.md) и Iterator (стандартный SPL интерфейс).

lmbSet содержит в себе набор данных в виде обычного массива и предоставляет к ним различные методы доступа.

lmbSet содержит также некоторые дополнительные методы:

* **merge($properties_list)** — объединяет текущий набор данных с указанным.
* **getPropertyList()** — возвращает набор ключей набора данных
* **getArray($property_name)** — делает автоматический cast до массива. Если property не массив - возвращает пустой массив.
* **getInteger($property_name)** — делает автоматический cast до целого числа.

## Примеры использования:

    $set = new lmbSet(array('property1' => 'value1',
                          'property2' => 10));
    $set->getInteger('property2');
    $set->get('property1');
    foreach($set as $key => $value)
      echo $key . ' - ' . $value . "  "; // выведет property1 - value1  property2 - 10

Остальные примеры смотри в тестах: limb/core/tests/cases/lmbSetTest.class.php
