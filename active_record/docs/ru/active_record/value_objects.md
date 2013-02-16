# Поддержка ValueObjects
В lmbActiveRecord также встроена первоначальная поддержка так называемых [ValueObjects](http://en.wikipedia.org/wiki/Value_Objects). Это объекты каких-либо классов, которые хранятся lmbActiveRecord, однако в базе данных они не представлены отдельными таблицами и хранятся в виде одного или нескольких полей в тех же таблицах, что и сами активные записи.

ValueObjects описываются посредством атрибута **_composed_of**.

## Описание через геттер
Описание через геттер позволяет использовать, для хранения данных, только одно поле в таблице.

Рассмотрим описание на примере хранения широты и долготы в базе данных, внутри одного поля, используя ':', как разделитель кода:

    class GeoСoordinate extends lmbObject
    {
      const DELIMITER = ':';
      protected $latitude;
      protected $longitude;  
 
      function __construct($db_value)
      {
        list($latitude, $longitude) = explode(self::DELIMITER, $db_value);
        $this->latitude = $latitude;
        $this->longitude = $longitude;
      }
 
      function getDbValue()
      {
        return $this->latitude . self::DELIMITER . $this->longitude;
      }  
    }
 
    class Event extends lmbActiveRecord
    {
      protected $_composed_of = array('place' => array('field' => 'coords',
                                                       'class' => 'GeoСoordinate',
                                                       'getter' => 'getDbValue'));
    }

Рассмотрим описание ValueObject (свойство **_composed_of**):

* **place** — это имя своства, по которому мы будем обращаться к ValueObject
* **coords** — имя поля в БД, в котором храняться данные ValueObject
* **GeoСoordinate** — имя класса ValueObject
* **getDbValue** — имя геттера, который будет вызван у объекта ValueObject для получения данных, которые нужно сохранить в БД. Если геттер не задан, то будет вызван метод **getValue()**.

## Описание через маппинг полей
Описание *ValueObject* через маппинг позволяет использовать несколько полей в БД.

    class Address extends lmbObject
    {
      protected $first;
      protected $last;
 
      //init something
      function init() {}
 
      function getFull()
      {
        return $this->first . ' ' . $this->last;
      }
    }
 
    class Member extends lmbActiveRecord
    {
      protected $_composed_of = array('name' => array('class' => 'Name',
                                                      'mapping' => array('first' => 'first_name',
                                                                         'last' => 'last_name'),
                                                      'setup_method' => 'setupName'));                                                  
 
      function setupName(Name $name)
      {    
        $name->init();
        return $name;
      }
    }

Рассмотрим описание *ValueObject* (свойство **_composed_of**):

* **name** — это имя своства, по которому мы будем обращаться к *ValueObject*
* **mapping** — описание маппинга свойств *ValueObject* на поля БД. Ключами являются свойства, значениями — названия полей БД. Для установки значения свойств будут вызваны методы *setFirst($value)* и *setLast($value)*, а для сохранения в базе *getFirst()* и *getLast()*.
* **Name** — имя класса *ValueObject*
* **setup_method** — имя setup-метода. Параметр необязателен.
