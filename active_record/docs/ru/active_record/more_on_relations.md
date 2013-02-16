# Дополнительная информация по отношениям
## Поддержка отношений в методе lmbActiveRecord :: import()
Метод lmbActiveRecord :: import() используется для заполнения/обновления объектов. Если метод import() встречает в списке переданных ему полей имя, сответствующее названию одного из отношений, он пытается заполнить полученные данные в соответствие с описанием отношения.

### Для отношений один-к-одному
Возьмем пример, описанный в разделе [«Поддержка отношений вида один-к-одному»](./one_to_one.md):

    class Person extends lmbActiveRecord
    {
      protected $_has_one = array('social_security' => array('field' => 'social_security_id',
                                                             'class' => 'SocialSecurity',
                                                             'can_be_null' => true));
    }
 
    class SocialSecurity extends lmbActiveRecord
    {
      protected $_belongs_to = array('person' => array('field' => 'social_security_id',
                                                       'class' => 'Person'));
    }

Используем import для того, чтобы связать SocialSecurity с Person.

    $person = new Person(); 
    $person->setName('Jim');
    $person->save();
    [[...]]
    $data = array('code' => '099123', 'person' => $person->getId());
    $number = new SocialSecurity();
    $number->import($data);
    $number->save();

Обратите внимание, что поле называется именно person — по названию связи, а не person_id.

Где может использоваться такая возможность? Обычно она используется в панелях управления, где можно передавать идентификатор связанных объектов прямо в запросе и передавать данные в объекты посредством вызовов вида:

    $number = new SocialSecurity();
    $number->import($request->export());
    $number->save();

Отметим, что это такой способ заполнения объекта данными подходит не всегда: например, если значение поля, соответствующее связи, в пришедшем в import() массиве не указывает на реальный объект. В этом случае может произойти исключение, так как при импорте lmbActiveRecord постарается загрузить указанный объект через findById() метод (см. [«Создание, сохранение, загрузка и удаление объектов»](./crud.md)).

### Для отношений один-ко-многим и много-ко-многим
Если в import в соответствующем поле пришел массив идентификаторов, эти объекты будут сохранены, как если бы мы вызвали метод setRelationName($objects).

Возьмем пример, описанный в разделе [«Поддержка отношений вида много-ко-многим»](./many_to_many.md):

    class Group extends lmbActiveRecord
    {
      protected $_db_table_name = 'user_group';
      protected $_has_many_to_many = array('users' => array('field' => 'group_id',
                                                            'foreign_field' => 'user_id',
                                                            'table' => 'user2group',
                                                            'class' => 'User'));
    }
 
    class User extends lmbActiveRecord
    {
      protected $_has_many_to_many = array('groups' => array('field' => 'user_id',
                                                             'foreign_field' => 'group_id',
                                                             'table' => 'user2group',
                                                             'class' => 'Group'));
    }

Используем import для того, чтобы связать User с Group.

    $group1 = new Group(); 
    $group1->setTitle('First group');
    $group1->save();
 
    $group2 = new Group(); 
    $group2->setTitle('Second group');
    $group2->save();
    [[...]]
    $data = array('first_name' => 'Vasa', 'groups' => array($group1->getId(), $group2->getId()));
    $user = new User();
    $user->import($data);
    $user->save(); // Теперь пользователь Vasa входит в 2 группы.
    // или
    $data = array('first_name' => 'Vasa', 'groups' => array($group1, $group2));
    $user = new User();
    $user->import($data);
    $user->save(); // Теперь пользователь Vasa входит в 2 группы.

Аналогично для отношений один-ко-многим.

## Работа с коллекциями связанных объектов
Рассмотрим отношение один-ко-многим, которое мы приводили чуть выше:

    class Course extends lmbActiveRecord
    {
      protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                       'class' => 'Lecture'));
    }
 
    class Lecture extends lmbActiveRecord
    {
      protected $_belongs_to = array('course' => array('field' => 'course_id',
                                                       'class' => 'Course'));
    }

Наша задача — рассмотреть подробнее коллекции связанных объектов, то, какими методами можно пользоваться, чтобы изменять состояние этих коллекций.

### Изменение содержимого коллекции
Итак, коллекцию, которую возвращает метод getLectures() можно изменять, например, добавлять в нее новые элементы:

    $course = new Course();
    $course->setTitle('Super course');
 
    $l1 = new Lecture();
    $l1->setTitle('Physics');
 
    $lectures = $course->getLectures();
    $lectures->add($l1); // Эквивалентно $course->addToLectures($l1);

Также из коллекции можно удалить все объекты, используя метод removeAll().

### Доступ к элементам внутри коллекции
Коллекция связанных объектов — это обычный итератор:

    $lectures = $course->getLectures();
    for($lectures->rewind(); $lectures->valid(); $lectures->next())
    {
      $lecture = $lectures->current();
      echo $lecture->getTitle();
    }
 
    // Возможна более компактная запись
    foreach($course->getLectures() as $lecture)
      echo $lecture->getTitle();

Можно получать доступ к определенным объектам по индексу при помощи метода **at($pos)**, это полезно при тестировании:

    $lectures = $course->getLectures();
    echo $lectures->at(2)->getTitle();

Обратите внимание, что каждый вызов at() приводит к отдельному запросу к базе данных.

Также можно получить массив объектов при помощи метода **getArray()** и обращаться к объектами по порядковому номеру:

    $lectures = $course->getLectures()->getArray();
    echo $lectures[1]->getTitle();

Также можно получить только список идентификаторов связанных объектов при помощи метода getIds():

    $ids = $course->getLectures()->getIds();
    echo implode(',', $ids); // Выведет что-то вроде 2,10,11,20...

### Поиск элементов в коллекциях
Для поиска элементов внутри коллекция существуют find-методы, подобные тем, которые реализованы для класса lmbActiveRecord.

Метод **find($params = array())** — осуществляет поиск элементов внутри коллекции. Аргумент $params действует аналогично тому, что используется в lmbActiveRecord :: find(). То есть вместо $params, можно передать строку или объект [Criteria](../../../../dbal/docs/ru/dbal/criteria.md), а может передать массив с некоторыми полями, которые будут содержать информацию по сортировке, например:

    $course = new Course();
    $short_lectures = $course->getLectures()->find('duration < 30');
    $criteria = new lmbSQLFieldCriteria('duration ', 60, '>');
    $sort = array('title' => 'DESC', 'duration' => 'ASC');
    $course->getLectures()->find(array('criteria' => $criteria, 'sort' => $sort));

Также существует метод **findFirst($params = array())**, который возвращает первый найденный элемент из коллекции.

### Сортировка
Если find-методы ничего не получают про то, как нужно сортировать элементы при итерации, тогда они используют параметры сортировки по-умолчанию.

Параметры сортировки по-умолчанию также применяются при обычном итерировании по элементам коллекции.

Параметры сортировки по-умолчанию формируются следующим образом:

* они могут быть указаны в описании отношений,
* они берутся из свойств класса, объекты которого хранятся в коллекции.

В первом случае сортировка может быть указана в виде параметра sort_params при описании отношений, например:

    class Course extends lmbActiveRecord
    {
      protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                       'class' => 'Lecture',
                                                       'sort_params' => array('title' => 'ASC')));
    }

Обратите внимание на **sort_params**. То есть теперь элементы коллекции lectures будут сортироваться по заголовку, если не будет указано иное:

    $lecture1 = new Lecture(array('title' => 'Super lecture'));
    $lecture2 = new Lecture(array('title' => 'Basic lecture'));
 
    $course = new Course(array('title' => 'My lecture'));
    $course->setLectures(array($lecture1, $lecture2));
    $course->save();
 
    $lectures = $course->getLectures()->find();
    $lectures->rewind();
    echo $lectures->current()->getTitle(); // Выведет Basic lecture
    $lectures->next();
    echo $lectures->current()->getTitle(); // Выведет Super lecture

Если же параметр sort не будет указан, тогда элементы коллекции (пусть будет lectures), будут сортироваться так, как это определено для класса Lecture (см. [сортировку в классе lmbActiveRecord](./find.md)).

### Различия в поведении коллекций для новых и уже сохраненных объектов
Обратите внимание, что коллекции отношений по разному себя ведут если родительский объект (владелец коллекции) является новым, то есть еще несохраненным в базе, и если он уже был сохранен.

Для коллекции уже сохраненного владельца вызов метода add(), find(), findFirst(), removeAll() приводят к обращению к базе данных. После вызова add() новый объект сразу же сохраняется (в случае с много-ко-многим еще добавляется запись в связывающую таблицу).

Для коллекции несохраненного владельца метод add() и removeAll() не приводят к обращению к базе данных. Они лишь изменяют состояние набора объектов в памяти. То есть в «новой» коллекции объекты «копятся», и сохраняются только при сохранении владельца. Обратите внимание, что метод find() и findFirst() для «новых» коллекций не работают и генерируют исключение.

## Изменение объектов в коллекциях
Если вы получили коллекцию связи один-ко-многим или много-ко-многим через родительский объект (или просто объект с одной из строн), и изменяете их, итерируя по коллекции, вы должны сохранять объекты явно. Сохранение только родительского объекта в текущей версии не достаточно:

    $course = new Course($course_id);
    foreach($course->getLectures() as $lecture)
      $lecture->setTitle($lecture->getTitle() . ' appended');
 
    $course->save(); // Не приведет к сохранению изменений в lectures.

Необходимо писать:

    $course = new Course($course_id);
    foreach($course->getLectures() as $lecture)
    {
      $lecture->setTitle($lecture->getTitle() . ' appended');
      $lecture->save();
    }

В будущих версиях это поведение планируется поправить, однако сейчас - будьте внимательны!

## Установка полей отношений напрямую
lmbActiveRecord для отношений **has_one** и **many_belongs_to**, то есть для тех, которые предусматривают наличие поля связи в таблице класса, работу с этими полями напрямую.

То есть, можно взять и поставить значение соответствующего поля явно, например:

    $lecture->set('course_id', $course_id);

В этом случае в таблицу lecture в поле course_id запишется $course_id.

Однако, если вы в это же время, попробуете работать со связанным объектом, тогда ранее поставленное поле напрямую уже учитываться не будет, то есть:

    $lecture->set('course_id', $course_id);
    $lecture->setCourse($my_course); // будет использован $my_course->getId() для поля course_id вместо $course_id

Напомним, что если вместо:

    $lecture->set('course_id', $course_id);

вызвать:

    $lecture->set('course', $course_id);

то второй пример приведет к загрузке объекта Course с идентификатором $course_id.

## Обнуление связей
lmbActiveRecord для отношений **has_one** и **many_belongs_to** также предусмотрено обнуление отношения. Для этого нужно или в качестве объекта связи указать null, или в значение поля таблицы указать null, например:

    $lecture->setCourse(null);
    // или 
    $lecture->set('course_id', null);

Убедитесь, что описание отношения имеет флаг **can_be_null**.

## Дополнительные возможности при описании отношений

При наличии сложных иерархий в рамках ActiveRecord возникает ситуация, когда в дочерних классах появляется необходимость расширить описания отношений, по сравнению с родительским классом. Для этих целей есть метод **_defineRelations()**, который вызывается в конструкторе lmbActiveRecord.

Например:

    class BaseNews extends lmbActiveRecord
    {
      protected $_db_table_name = 'news';
 
      protected $_has_many = array('publications_in_sections' => array('field' => 'news_id',
                                                                       'class' => 'SectionNewsPublish'),
                                   'related_links' => array('field' => 'news_id',
                                                            'class' => 'NewsRelatedLinks'),
                                   'related_news' => array('field' => 'news_id',
                                                           'class' => 'rtNewsRelatedNews'));
 
    }
 
    class VersionedNews extends BaseNews
    {
      protected function _defineRelations()
      {
         $this->_has_many['version'] = array('field' => 'original_id',
                                             'class' => 'NewsVersion',
                                             'sort_params' => array('version' => 'DESC'));
      }
    }

Расширять описания отношений можно как напрямую, добавляя элементы в соответствующие атрибуты класса, так и при помощи методов, например, _hasOne($relation_name, $relation), _hasMany($relation_name, $relation) и т.д.:

    class VersionedNews extends BaseNews
    {
      protected function _defineRelations()
      {
         $this->_has_many('version', array('field' => 'original_id',
                                           'class' => 'NewsVersion',
                                           'sort_params' => array('version' => 'DESC'));
      }
    }

Какой способ предпочесть — зависит от вашего вкуса.

### Динамическое описание отношений
При необходимости, можно динамически указывать отношения. Например, такой код в зависимости от наличия в таблице поля lmb_cms_user_id задаст соответствующее отношение

    function _defineRelations()
    {
      if ($this->has('lmb_cms_user_id'))
      {
        $this->_hasOne('owner', array('field' => 'lmb_cms_user_id',
                                      'class' => 'lmbCmsUser'));
      }
    }

## Условия в отношениях
Иногда возникает необходимость иметь в наследнике ActiveRecord метод, который не просто получает коллекцию связанных объектов, но и накладывает на нее определенные условия выборки. В этом случае нужно задать параметр criteria в описании отношений.

    protected $_has_many_to_many = array('groups' => array('field' => 'user_id',
                                                           'foreign_field' => 'group_id',
                                                           'table' => 'user2group',
                                                           'class' => 'Group'),
                                         'active_groups' => array('field' => 'user_id',
                                                           'foreign_field' => 'group_id',
                                                           'table' => 'user2group',
                                                           'class' => 'Group',
                                                           'criteria' =>'`group`.`is_active`=1'
                                                                  ));

После создания такого описания отношений мы будем иметь два метода: getGroups для получения всех связанных с пользователем групп и getActiveGroups — для получения связанных с пользователем групп, у которых признак активности равен единице.

    $groups = $user->getGroups();
    // или
    $groups = $user->getActiveGroups();

Возможность накладывать criteria на коллекции связанных объектов поддерживается как для _has_many_to_many, так и для _has_many.

## Использование 1-й таблицы для нескольких отношений
lmbActiveRecord позволяет иметь несколько отношений many_to_many (и другие, если необходимо) в одной таблице. Например, у нас был случай, когда таблица, которая хранила связи, была следующей:

    CREATE TABLE `related_forum_topic` (        
    `id` BIGINT(20) NOT NULL AUTO_INCREMENT,  
    `topic_id` BIGINT(20) DEFAULT NULL,       
    `is_main` tinyint(4) DEFAULT NULL,        
    `section_id` BIGINT(20) DEFAULT NULL,     
    `news_id` BIGINT(20) DEFAULT NULL,        
    PRIMARY KEY  (`id`)                       
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8

А связи описывались следующим образом:

    class News extends lmbActiveRecord
    {
      protected $_has_many_to_many = array('related_forum_topics' => array('field' => 'news_id',
                                                                           'foreign_field' => 'topic_id',
                                                                           'class' => 'ForumTopic',
                                                                           'table' => 'related_forum_topic'));
    }
 
    class Section extends lmbActiveRecord
    {
      protected $_has_many_to_many = array('related_forum_topics' => array('field' => 'section_id',
                                                                           'foreign_field' => 'topic_id',
                                                                           'class' => 'ForumTopic',
                                                                           'table' => 'related_forum_topic'));
    }
 
    class ForumTopic extends lmbActiveRecord
    {
      protected $_has_many = array('link_to_me' => array('field' => 'topic_id',
                                                         'class' => 'RelatedForumTopic'));
 
      protected $_has_many_to_many = array('sections_links' => array('field' => 'topic_id',
                                                                     'foreign_field' => 'section_id',
                                                                     'class' => 'Section',
                                                                     'table' => 'related_forum_topic'),
                                           'news_links' => array('field' => 'topic_id',
                                                                 'foreign_field' => 'news_id',
                                                                 'class' => 'News',
                                                                 'table' => 'related_forum_topic'));
    }
 
    class RelatedForumTopic extends lmbActiveRecord
    {
      protected $_many_belongs_to = array('topic' => array('field' => 'topic_id',
                                                           'class' => 'ForumTopic'));
    }

Таким образом, мы хранили 2 отношения many-to-many и 1 one-to-many в 1 таблице related_forum_topic. При обновлении записей отношений 'sections_links' и 'news_links' класса ForumTopic из таблицы related_forum_topic удалялись только те записи, где значение foreign_field не было равно null.
