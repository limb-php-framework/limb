# Создание, сохранение, загрузка и удаление объектов
[ActiveRecord](http://en.wikipedia.org/wiki/ActiveRecord) — это посредник между клиентским кодом и одной записью одной таблицы базы данных.

## Таблицы базы данных
В процессе описания, мы будем опираться на класс User и таблицу user, с которыми мы работали в примере на странице [«Краткое описание»](./intro.md).

Таблица **user** содержит автоинкрементное поле **id**. Это суррогатный первичный ключ. Его наличие обязательно во всех таблицах, с которыми вы работаете через класс lmbActiveRecord.

**lmbActiveRecord** пытается самостоятельно определить, с какой таблицей стоит работать по названию класса. Если мы создали дочерний класс **BasePlan**, тогда по-умолчанию этот класс будет работать с таблицей base_plan. То есть название класса преобразуется из CamelCase в under_scores.

Вы можете самостоятельно задать имя таблицы, с которой следует работать дочернему классу посредством определения атрибута **$db_table_name**:

    class User extends lmbActiveRecord
    {
      protected $_db_table_name = 'my_user';
    }

lmbActiveRecord использует в своей работе класс [lmbTableGateway](../../../..//dbal/docs/ru/dbal/lmb_table_gateway.md), который обеспечивает все низкоуровневневые операции по считыванию и записи данных из соответствующей таблицы базы данных. Также lmbTableGateway считывает информацию о структуре таблицы, о типах полей и обеспечивает фильтрацию полей и type castring, когда это необходимо.

## Создание и заполнение объектов
Можно заполнять объекты по одному полю, например:

    $user = new User();
    $user->setName('Vasa');
    $user->setLastName('Pupkin');
    $user->getName();        // Выведет Vasa
    $user->getLastName();   // Выведет Pupkin

или так:

    $user = new User();
    $user->set('name', 'Vasa');
    $user->set('last_name', 'Pupkin');
    $user->get('name');        // Выведет Vasa
    $user->get('last_name');   // Выведет Pupkin

Так как lmbActiveRecord наследуется от [lmbObject](../../../../core/docs/ru/core/lmb_object.md), он поддерживает магические getter-ы и setter-ы, например, getName() и setName() даже если этих методов нет.

Также можно передавать массив данных прямо в конструктор:

    $data = array('name' => 'Vasa', 
                'last_name' => 'Pupkin');
    $user = new User($data);

Для заполнения и получения данных из объектов можно также использовать методы **import()** и **export()**. В качестве агрумента метода **import()** можно использовать массив, другой объект [ActiveRecord](http://en.wikipedia.org/wiki/ActiveRecord) или любой иной контейнер данных, который поддерживает метод export(), например, запись из базы данных или же объект класса lmbSet, например:

    $user1 = new User();
    $user1->import(array('name' => 'Vasa', 'last_name' => 'Pupkin'));
 
    $user2 = new User();
    $user2->import(lmbSet(array('name' => 'Vasa', 'last_name' => 'Pupkin')));
 
    $other_user = new User(array('name' => 'Vasa', 'last_name' => 'Pupkin'));
    $user3 = new User();
    $user3->import($other_user);
    
## Сохранение объектов
Метод lmbActiveRecord :: **save()** создает в таблице новую запись, если объект является новым. По-умолчанию объект является новым, если у него еще нет идентификатора (значения в поле **id**) . Если объект уже существует, когда при вызове save() запись будет обновлена.

Метод lmbActiveRecord :: save() возвращает идентификатор созданной или измененной записи.

    $user = new User();
    $user->setName('Vasa');
    $user->save(); // Вернет сгенерированный идентификатор.
    
Для получения информации, является ли объект новым, можно использовать метод lmbActiveRecord :: **isNew()**. Метод lmbActiveRecord :: **isDirty()** возвращает true, если объект содержит данные, которых еще нет в базе данных (новые или изменение в процессе работы после загрузки). При помощи метода lmbActiveRecord :: **isDirtyProperty($property_name)** можно узнать, была ли изменено определенное поле. Все это лучше продемонстрировать небольшим куском кода:

    $user = new User();
    $user->setName('Vasa');
    $user->isNew();  // Вернет true
    $user->isDirty();  // Вернет true
    $user->save();
 
    $user->isDirty();  // Вернет false
    $user->isNew();  // Вернет false
 
    $user->setName('Ivan');
 
    $user->isNew();  // Вернет false
    $user->isDirty();  // Вернет true
 
    $user->save();
    $user->isDirty();  // Вернет false
    
## Загрузка объектов
Для загрузки ранее сохраненного объекта по имеющемуся идентификатору можно использовать передачу этого идентификатора в конструктор, например:

    $user = new User($user_id);

Если запись с таким идентификатором не будет найдена, будет сгенерировано исключение класса **lmbARNotFoundException**. Например:

    try{
    $user = new User(-100);
    }
    catch(lmbARException $e){
     addError('Cant load object!');
    }

Также можно использовать статический метод lmbActiveRecord :: **findById($class_name, $id)**:

    $user = lmbActiveRecord :: findById('User', $user_id);
    
Если запись с указанным идентификатором не будет найдена, также будет сгенерировано исключение класса lmbARNotFoundException, как и в случае передачи идентификатора в конструктор.

Если вы не желаете получать исключения, тогда используейте третий параметр $throw_exception = false, например:

    $user = lmbActiveRecord :: findById('User', $user_id, $throw_exception = false);

К сожалению, в PHP пока практически невозможно сделать нечто подобное $user = User :: findById($user_id) (то есть получить именно объект класса User) без применения средств автогенерации или ручного добавления метода findById в класс User. Поэтому приходится писать именно lmbActiveRecord :: findById('User'…);

Третий вариант загрузки метод lmbActiveRecord :: loadById($id), например:

    $user = new User();
    $user->loadById($user_id);
    
Наше личное мнение заключается в том, что нужно все же использовать статический метод findById вместо loadById(тем более, что внутри loadById реализован через findById).

Если вам нужно загрузить объект по какому-либо условию, можно воспользоваться методом lmbActiveRecord :: **findFirst($class_name, $params = array())**. В качестве аттрибута второго параметра criteria можно передавать чаcть SQL-запроса или объект из набора [Criteria](../../../../dbal/docs/ru/dbal/criteria.md). Также можно задать правила сортировки, используя аттрибут sort, передав ассоциативный массив где ключ — поле, а значение — способ сортировки. Например:

    $criteria = new lmbSQLFieldCriteria('name', '%Vasa%', lmbSQLFieldCriteria :: LIKE);
    $sort = array('name' => 'DESC', 'last_name' => 'ASC');
    $user = lmbActiveRecord :: findFirst('User', array('criteria' => $criteria, 'sort' => $sort));

Универсальный статический метод lmbActiveRecord :: **find($class, $params = array())** используется для загрузки сразу нескольких объектов с возможностью применения сортировки и фильтрации по некоторому условию. Метод find() возвращает итератор c объектами. То есть, с каждым элементов можно работать, как с полноценным объектом класса lmbActiveRecord. Более того, этот итератор поддерживает интерфейс [lmbCollectionInterface](../../../../core/docs/ru/core/lmb_collection_interface.md).

Подробнее о различных find()-методах будет рассказано в соответствующем разделе [«Поиск и сортировка объектов»](./find.md).

## Удаление

Для удаления записи из таблицы используется метод lmbActiveRecord :: **destroy()**. Этот метод используется если объект уже был загружен или же он имеет идентификатор, например, проставленный вручную, например:

    $user = new User($user_id);
    $user->destroy();
 
    $user = new User();
    $user->setId($user_id);
    $user->destroy();

Для удаления нескольких объектов используется статический метод lmbActiveRecord :: **delete($class, $params = array())**. В качестве аттрибута criteria второго аргумента можно передавать часть SQL-запроса, или же объект одного из классов [Criteria](../../../../dbal/docs/ru/dbal/criteria.md). Например:

    lmbActiveRecord :: delete('User', 'id=100');
    lmbActiveRecord :: delete('User', new lmbSQLFieldCriteria('name', '%Vasa%', lmbSQLFieldCriteria :: LIKE));
    
Если критерий не задан, то будут удалены все объекты заданного класса.

    lmbActiveRecord :: delete('User'); //удалит все объекты User, загружая каждый объект в память.

Обратите внимание, что при использовании метода delete() объекты не будут удалены одним DELETE запросом. Объекты будут загружены из базы данных при помощи find() метода и удалены по-одному. Это сделано для того, чтобы обеспечивать целостность данных при наличии какой-либо бизнес-логики при удалении и в случае, если ваши **ActiveRecord-ы** находятся в различных отношениях. Если вам необходимо обеспечивать быстрое удаление без дополнительных проверок, этот код нужно будет создавать вручную.

## Изменение состояние таблицы напрямую без загрузки объектов ActiveRecord

Для удаления записей из соответствующей таблицы напрямую, без загрузки каждого объекта, можно использовать метод lmbActiveRecord :: **deleteRaw($class_name, $criteria = null)**, например:

    lmbActiveRecord :: deleteRaw('User'); //удалит все объекты User одним запросом

Для обновления записей можно использовать метод lmbActiveRecord :: **updateRaw($class_name, $set, $criteria = null)**, например:

    lmbActiveRecord :: update('user', array('access' => 10), lmbSQLCriteria :: equal('login', 'vasa'));

Обратите внимание, что в этом случае вся информация об отношениях или какая еще логика, связанная с удалением или обновлением (кроме той, что реализована в самой базе данных), будет игнорирована.
