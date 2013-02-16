# Поддержка отношений вида много-ко-многим
## Определение отношения
Рассмотрим связь вида много-ко-многим на примере двух классов — пользователь (User) и набор групп пользователей (Group), при этом пользователи могу входить в несколько групп одновременно, а группы могу содержать много пользователей.

Объекты этих классов хранятся в таблицах соответственно user и user_group, а также существует таблица для хранения информации о связи много-ко-многим user2group:

    CREATE TABLE `user_group` (
      `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
      `title` VARCHAR(255) DEFAULT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
    CREATE TABLE `user` (
      `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
      `first_name` VARCHAR(255) DEFAULT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
    CREATE TABLE `user2group` (
     `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
     `user_id` BIGINT(20) DEFAULT NULL,
     `group_id` BIGINT(20) DEFAULT NULL,
     PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

Связь много-ко-многим моделируется в базе данных как правило через дополнительную таблицу, которая хранит первичные идентификаторы связанных объектов. Как мы уже отмечали Limb поддерживает только суррогатный автоинкрементный первичный ключ id, поэтому его также придется завести в таблице связей (если кому-то показалось, что можно обойтись составным первичным ключом).

Связь много-ко-многим описывается атрибутами **$_has_many_to_many** и с левой, и с правой стороны:

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

Одно отношение **$_has_many_to_many** описывается следующими полями:

* **table** — указывает на название таблицы, где хранятся связи. Обычно эта таблица содержит только 3 поля - два внешних ключа, и еще один автоинкрементный идентификатор. Мы предпочитаем давать этой таблице название по имени двух связывающих ею таблиц, соединяя эти два имени через «2». В нашем случае мы соединяет user и group, поэтому получилось user2group.
* **field** — указывает на название поле в таблице связей, в котором хранятся идентификаторы текущего класса.
* **foreign_field** — указывает на название поля, в котором хранятся идентификаторы связываемых объектов.
* **class** — указывает на название класса связываемых объектов.
* **collection** — указывает на название класса, через который реализуется связь. По-умолчанию это значение равно lmbARManyToManyCollection.

Повторим еще раз: связи описываются с двух сторон.

## Работа со связанными объектами
Использование связи много-ко-многим похожа на один-ко-многим:

    $user1 = new User();
    $user1->setFirstName('Bob');
 
    $user2 = new User();
    $user2->setFirstName('Alex');
 
    $group1 = new Group();
    $group1->setTitle('group1');
 
    $group2 = new Group();
    $group2->setTitle('group2');
 
    $user1->addToGroups($group1);
    $user1->addToGroups($group2);
 
    $group2->addToUsers($user2);
 
    $user1->save(); // Будут сохранены все группы и пользователи
 
    $user3 = new User();
    $user3->loadById($user1->getId());
 
    echo $user3->getGroups()->at(0)->getTitle(); // Выведет 'group1'
 
    $group3 = new Group();
    $group3->loadById($group2->getId());
 
    echo $group3->getUsers()->count(); // Выведет 2

### Связывание объектов
Для добавления новых связанных объектов по одному в базовый объект (с любой стороны) используется метод **addToRelationName($object)**, в нашем случае Group :: addToUsers($user) или User :: addToGroups($group):

    $user1 = new User();
    $user1->setFirstName('Bob');
 
    $user2 = new User();
    $user2->setFirstName('Alex');
 
    $group1 = new Group();
    $group1->setTitle('group1');
 
    $group2 = new Group();
    $group2->setTitle('group2');
 
    $user1->addToGroups($group1);
    $user1->addToGroups($group2);
 
    $group2->addToUsers($user2);
 
    $user1->save(); // Будут сохранены все группы и пользователи, а также связи между ними

Можно также добавить сразу несколько объектов, используя метод **setRelationName($array_or_iterator)**, в нашем случае Group :: setUsers($users) или User :: setGroup($groups). В качестве параметра передается массив или итератор с объектами.

    $user1 = new User();
    $user1->setFirstName('Bob');
 
    $user2 = new User();
    $user2->setFirstName('Alex');
 
    $group1 = new Group();
    $group1->setTitle('group1');
 
    $group1->setUsers(array($user1, $user2));
 
    $group1->save();  // Сохранит группу и пользователей, а также связи между ними

Обратите внимание, что при использовании метода setRelationName() набор связанных объектов полностью заменяется на новый. Однако, в отличие от связи [«один-ко-многим»](./one_to_many.md) связанные объекты не удаляются, удаляются только старые связи между объектами.

### Навигация по связанных объектам
Метод **getRelationName()** c любой строны связи, в нашем случае это будет или User :: getGroups(), или Group :: getUsers(), возвращает итератор (коллекцию) со связанными объектами:

    $user = lmbActiveRecord :: findById('User', $user_id);
 
    $groups = $user->getGroups();
    echo "User " . $user->getFirstName() . " is a member of the following groups: \n";
    foreach($groups as $group)
      echo $group->getTitle() . "\n";

Получив коллекцию связанных объектов можно добавлять в нее элементы посредством метода add($object), что эквивалентно вызову addToRelationName($object) у текущего объекта:

    $user1 = new User();
    $user1->setFirstName('Bob');
 
    $user2 = new User();
    $user2->setFirstName('Alex');
 
    $group1 = new Group();
    $group1->setTitle('group1');
 
    $users = $group1->getUsers();
    $users->add($user1);
    $users->add($user2);

### Удаление связанных объектов
При помощи метода **removeAll()** применительно к коллекции связанных объектов можно удалять связи между объектами (но не сами объекты), например:

    $user = lmbActiveRecord :: findById('User', $user_id);
    $user->getGroups()->removeAll(); // С этого момента пользователь не входит ни в одну группу

Если вам нужно удалить сами объекты, можете удалять его явно через метод lmbActiveRecord :: destroy() или lmbActiveRecord :: delete($class_name, $params = array()).

Для удаления одного объекта (связи) из коллекции используется метод **remove()**

    $user = lmbActiveRecord :: findById('User', $user_id);
    $user->getGroups()->remove($group_obj); // Пользователь исключён только из данной группы

Обратите внимание, что коллекции, в частности методы add() и removeAll() ведут себя по-разному, в зависимости от того, сохранен родительский объект в момент работы с коллекцией или еще нет. Подробнее об этом, а также дополнительная информация по работе с коллекциями связанных объектов можно получить в разделе [«Дополнительная информация по отношениям»](./more_on_relations.md).
