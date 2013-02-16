# Расширение классов коллекций связей вида один-ко-многим и много-ко-многим
## Создание и внедрение новых классов коллекций
Необходимость использовать свои собственные классы для коллекций связанных объектов может проявиться, если вам необходимо применять какие-либо особенные правила при выборках объектов или при изменении состояния коллекции.

Базовыми классами для коллекций являются:

* **lmbAROneToManyCollection** — для коллекций связей один-ко-многим.
* **lmbARManyToManyCollection** — для коллекция связей много-ко-многим.

Для внедрения новой коллекции используется параметр **collection** при описании связи, например:

    class Course extends lmbActiveRecord
    {
      protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                       'class' => 'Lecture',
                                                       'collection' => 'CourseLecturesCollection'));
    }
 
    class Lecture extends lmbActiveRecord
    {
      protected $_many_belongs_to = array('course' => array('field' => 'course_id',
                                                            'class' => 'Course'));
    }

В примере мы будем использовать CourseLecturesCollection при работе со списком лекций, которые относятся к курсу.

Класс CourseLecturesCollection может выглядеть следующим образом:

    class CourseLecturesCollection extends lmbAROneToManyCollection
    {
      function add($object)
      {
        if($this->_someImportantCondition())
          parent :: add($object);
        else
          throw new lmbException('This operation is not allowed');
      }
 
      protected _someImportantCondition()
      {
        [...]
      }
    }

## Условное внедрение новых классов коллекций
Иногда новый класс нужно внедрять на основе какой-либо логики. Коллекции создаются в методе lmbActiveRecord :: **createRelationCollection($relation, $criteria = null)**

В нашей работе мы встречались со случаем, когда необходимо было внедрять декоратор на коллекцию, если объекты в коллекции были определенного класса:

    function createRelationCollection($relation, $criteria = null)
    {
      $collection = parent :: createRelationCollection($relation, $criteria);
      $relation_info = $this->getRelationInfo($relation);
      $class = $relation_info['class'];
      $object = new $class();
      if($object instanceof SpecialObject)
        return new SpecialObjectCollectionDecorator($collection);
      else
        return $collection;
    }

## Наложение условий на все выборки в коллекции
Обратите внимание на второй параметр метода lmbActiveRecord :: createRelationCollection($relation, $criteria = null). Этот параметр позволяет внедрить в коллекцию условие, которое всегда будет накладываться на выборки элементов, помимо тех, что передаются в find()-методы.
