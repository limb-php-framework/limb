# lmbObject
**lmbObject** — это базовый класс, который используется для реализации объектов-хранилищ произвольных наборов данных. lmbObject поддерживает различные методы для манипуляции данными (добавление, объединение, очистка и т.д.), в том числе геттеры и сеттеры для любого поля (на самом деле атрибута класса), даже если этих методов нет (реализовано через перекрытие метода __call).

lmbObject реализует интерфейс [lmb_set_interface](./lmb_set_interface.md). см. также [Контейнеры данных в Limb3](./data_containers.md).

## Пример использования класса lmbObject

    $obj = new lmbObject(array('my_data' => 'Whatever'));
 
    echo $obj->getMyData(); // Выведет 'Whatever'
 
    $obj->setMyData('Something');
 
    echo $obj->getMyData(); // Выведет 'Something'
 
    echo $obj->get('my_data'); // Выведет 'Something'
 
    $obj->remove('my_data'); 
 
    echo $obj->getMyData(); // Выведет NULL

## Список наиболее часто используемых методов

Метод | Назначение
------|-----------
set($property, $value) | Устанавливает в поле (в аттрибут) $property значение $value
setXXX($value) | Устанавливает в поле $property, которое получается из части названия метода, значение $value. Часть названия метода после set переводится в under_scores, например setMyData - устанавливает значение в поле my_data.
get($property) | Получает значение поля $property.
getXXX() | Получает значение поля $property, которое получается из части назавния метода после get, например, getMyData возращает значение поля my_data.
has($property) | Возвращает true, если объект имеет поле $property.
import($values) | Устанавливает значения полей исходя из содержимого ассоциативного массива $values
export() | Возвращает массив значений всех полей (аттрибутов) объекта.
remove($property) | Удаляет поле $property из объекта.
reset() | Удаляет все поля из объекта.

## Защищенные поля

Защищенными считаются поля, название которых начинается с префикса в виде нижнего подчеркивания «_». Обычно такие атрибуты содержатся в дочерних классах lmbObject. Защищенные поля не присутствуют в результатах метода export() и не устанавливаются при помощи геттеров и сеттеров.

Например:

    class MyObject extends lmbObject
    {
      protected $_my_property = null;
 
      function setMyProperty($value){
        $this->_my_property = $value;
      }
 
      function getMyProperty(){
        return $this->_my_property;
      }
    }
 
    $obj = new MyObject();
    $obj->setMyProperty('Something');
    $obj->set('_my_property', 'Other value'); // не будет работать, так как _my_property - защищенное свойство!
    $obj->setOtherProperty('Whatever');
    $obj->export(); // Получим array('other_property' => 'Whatever');
    echo $obj->getMyProperty() ; // Выведет Something, а не Other value

## Некоторые тонкости в использовании lmbObject
Обратите внимание, что поддержка магических методов и наличие защищенных свойство может внести неясности в работе класса. Поэтому на что следует обратитить внимание:

* В lmbObject есть методы getHash(), getClassName() — поэтому избегайте использования свойств hash и class_name.
* Методы import() и export() не импортируют и не экспортируют защищенные свойства (с префиксом _ )
* Если в вашем классе-дочернем от lmbObject, скажем, есть метод setMyProperty($value), то при вызове метода set('my_property', $value) будет вызван именно этот метод. Это значит, что дочерний класс не должен вызывать метод set('my_property', $value) внутри метода setMyProperty() иначе будет рекурсия. Для этих целей нужно использовать метод lmbObject :: _setRaw($property_name, $value), который не содержит проверки на наличие метода вида setMyProperty()
