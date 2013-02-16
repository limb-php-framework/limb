# Поддержка отношений вида один-ко-многим
## Определение отношения
Рассмотрим связь вида один-ко-многим на примере двух классов — курс (Course ) и набор лекций, из которых он состоит (Lecture). В итоге имеем отношение Course 1 — *(has many) Lectures.

Объекты этих классов хранятся в таблицах соответственно course и lecture:

    CREATE TABLE `course` (
     `id` INT(11) NOT NULL AUTO_INCREMENT,
     `title` VARCHAR(255) DEFAULT NULL,
     PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
    CREATE TABLE `lecture` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `title` VARCHAR(255) DEFAULT NULL,
      `course_id` INT(11) DEFAULT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

Обратите внимание на поле course_id в таблице lecture. Это внешний ключ на поле id таблицы course. Получается, что поле, которые хранит связь, находится в таблице подчиненного объекта. В этом заключается отличие от связи [«Один-к-одному»](./one_to_one.md), где связь хранится в поле таблицы главного (родительского) объекта.

Связь один-ко-многим в классах lmbActiveRecord описывается атрибутами **$_has_many** того класса, который является главным в связи и **$_many_belongs_to** того класса, который является подчиненным. В нашем случае классы Course и Lecture будут выглядеть следующим образом:

    class Course extends lmbActiveRecord
    {
      protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                       'class' => 'Lecture'));
    }
 
    class Lecture extends lmbActiveRecord
    {
      protected $_many_belongs_to = array('course' => array('field' => 'course_id',
                                                            'class' => 'Course'));
    }

Одно отношение **$_has_many** описывается следующими полями:

* **field** — указывает на поле в таблице подчиненного класса (в нашем случае таблица lecture), в котором хранится значение идентификатора родительского объекта.
* **class** — указывает на название класса подчиненного объекта.
* **nullify** — указывает на то, что при удалении не нужно удалять дочерние объекта, а нужно лишь обновить для них поле **field** в **null**.
* **collection** — указывает на название класса, через который реализуется связь. По-умолчанию это значение равно lmbAROneToManyCollection.

Одно отношение $_many_belongs_to описывается следующими полями:

* **field** — указывает на поле в таблице текущего класса (подчиненный), в котором хранится значение идентификатора родительского объекта.
* **class** — указывает на название класса родительского объекта.
* **can_be_null** — указывает на то, что родительский объект может и не быть указан, то есть связь необязательная.

Обратите внимание, что course_id упоминается как в $_has_many, так и в $_many_belongs_to.

## Работа со связанными объектами
### Связывание объектов
Для добавления новых зависимых объектов по одному в родительский объект (содержащий $_has_many) используется метод **addTo*RelationName*($object)**, в нашем случае addTo*Lectures*($lecture):

    $course = new Course();
    $course->setTitle('Super course');
 
    $l1 = new Lecture();
    $l1->setTitle('Physics');
    $l2 = new Lecture();
    $l2->setTitle('Math');
 
    $course->addToLectures($l1);
    $course->addToLectures($l2);

Можно также добавить сразу несколько объектов, используя метод **set*RelationName*($array_or_iterator)**, в нашем случае set*Lectures*($lectures). В качестве параметра передается массив или итератор с объектами.

    $course = new Course();
    $course->setTitle('Super course');
 
    $l1 = new Lecture();
    $l1->setTitle('Physics');
    $l2 = new Lecture();
    $l2->setTitle('Math');
 
    $course->setLectures(array($l1, $l2));
 
    $course->save();  // Сохранит курс и связанные с ним лекции

Обратите внимание, что при использовании метода set*RelationName*() набор связанных объектов полностью заменяется на новый, старые связанные объекты — удаляются.

### Навигация по связанных объектам
Метод **get*RelationName*()** родительского класса применительно к связанным один-ко-многим объектам(в нашем случае это будет Course :: get*Lectures*()) возвращает итератор (коллекцию) с дочерними объектами

    $course = lmbActiveRecord :: findById('Course', $course_id);
 
    $lectures = $course->getLectures();
    foreach($lectures as $lecture)
      echo $lecture->getTitle() . "\n";

Получив коллекцию связанных объектов можно добавлять в нее элементы посредством метода add($object), что эквивалентно вызову addT*oRelationName*($object) у родительского объекта:

    $course = new Course();
    $course->setTitle('Super course');
 
    $l1 = new Lecture();
    $l1->setTitle('Physics');
    $l2 = new Lecture();
    $l2->setTitle('Math');
 
    $lectures = $course->getLectures();
    $lectures->add($l1);
    $lectures->add($l2);

При помощи метода get*RelationName*() у подчиненного объекта (в нашем случае это метод Lecture :: get*Course*()) можно получить родительский объект:

    $lectures = lmbActiveRecord :: find('Lecture');
 
    foreach($lectures as $lecture)
      echo "Lecture " . $lecture->getTitle() . " of " . $lecture->getCourse()->getTitle() . " course. \n";

В последнем примере мы сталкиваемся с проблемой n+1 выборок, то есть для каждой лекции будет сделан запрос на курс. Как решить эту проблему, мы расскажем позже.

см. также [«Дополнительная информация по отношениям»](./more_on_relations.md)

### Явное удаление связанных объектов
При помощи метода **removeAll()** применительно к коллекции связанных объектов можно удалять связанные объекты, например:

    $course = lmbActiveRecord :: findById('Course', $course_id);
    $course->getLectures()->removeAll();

Если вам нужно удалить все лишь один объект из коллекции, можете удалить его явно через метод lmbActiveRecord :: destroy().

removeAll() приводит к удалению связанных объектов по-одному с предварительной загрузкой их в память.

Обратите внимание, что коллекции, в частности методы add() и removeAll() ведут себя по-разному, в зависимости от того, сохранен родительский объект в момент работы с коллекцией или еще нет. Подробнее об этом, а также дополнительная информация по работе с коллекциями связанных объектов можно получить в разделе [«Дополнительная информация по отношениям»](./more_on_relations.md).
 
## Удаление родительского объекта
При удалении объекта, который имеет связь has_many по-умолчанию просходит вызов метода removeAll() для коллекции дочерних элементов. То есть **по-умолчанию происходит каскадное удаление**.

Однако has_many отношение поддерживает так называемый **nullify**, когда мы просто обнуляем значение соответствующего поля в таблице дочернего класса, например:

    class Course extends lmbActiveRecord
    {
      protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                       'class' => 'Lecture',
                                                       'nullify' => true));
    }
 
    class Lecture extends lmbActiveRecord
    {
      protected $_many_belongs_to = array('course' => array('field' => 'course_id',
                                                            'class' => 'Course',
                                                            'can_be_null' => true));
    }

Обратите внимание на то, что в описании many_belongs_to класса Lecture появилась опция **can_be_null**. Это обязательное условие, если мы используем nullify для Course.
