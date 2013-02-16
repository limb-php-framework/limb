# lmbSimpleDb
Класс **lmbSimpleDb** — небольшой фасад для пакета, который позволяет упростить выполнение простых CRUD запросов.

## Инициализация
Конструктор класса lmbSimpleDb принимает объект подключения к базе данных (connection):

    lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
    $connection = lmbToolkit :: instance()->getDefaultDbConnection();
    $db = new lmbSimpleDb($connection);

Более простым способом создания объекта lmbSimpleDb с подключением к базе данных по-умолчанию является вызов метода [lmbDBAL](./lmbdbal.md) :: db(), например:

    $db = lmbDBAL :: db();
 
Или, если вы используете частное подключение:

    $db = lmbDBAL :: db($my_connection);

## Выполнение select-запросов
lmbSimpleDb содержит следующие методы для выполнения select-запросов:

* **select($table, $criteria = null, $order = array())** — выполняет запрос в таблицу $table с условием $criteria, сортируя записи по признакам $order. Возвращает набор данных ($record_set), поддерживающий интерфейс lmbDbRecordSet
* **selectRecord($table, $criteria = null)** — выполняет запрос в таблицу $table с условием $criteria и возвращает первую запись или null, если ничего не было выбрано. Возвращает объект записи ($record), поддерживающий интерфейс lmbDbRecord
* **count($table, $criteria = null)** — подсчитывает количество записей из таблицы $table при выборке с условием $criteria.

(см. [раздел Driver](./driver.md) об интерфейсах lmbDbRecordSet и lmbDbRecord)

В качестве **$criteria** можно передавать как часть $sql кода, так и объект [Criteria](./criteria.md).

Например:

    $db = new lmbSimpleDb($conn);
    $rs = $db->select('article', 'author_id = '. (int)$author_id);
    // или
    $rs = db->select('article', new lmbSQLCriteria('author_id = '. (int)$author_id));

Строковые данные в целях безапасности имеет смысл квотировать:

    $rs = $db->select('article', 'title LIKE '. $db->quote("%" . $search_title));

В качестве **$order** передается массив вида array('field' ⇒ 'order_type'), например:

    $db = new lmbSimpleDb($conn);
    $rs = $db->select('article', '', array('year' => 'DESC', 'title' => 'ASC'));

Зачастую вместо параметра $order удобнее все же использовать метод sort() самого record_set-а:

    $db = new lmbSimpleDb($conn);
    $rs = $db->select('article')->sort(array('year' => 'DESC', 'title' => 'ASC'))->paginate(0, 5);

Здесь мы выбрали первые 5 записей из таблицы article отсортировав из по году по убыванию и по загововку в алфавитном порядке.

Опять же вместо lmbSimpleDb :: count() можно использовать метод count() record_set-а, например:

    $db = new lmbSimpleDb($conn);
    $number = $db->count('article', 'author_id = '. $author_id);
    // или можно 
    $number = $db->select('article', 'author_id = '. $author_id)->count();

## Выполнение запросов на модификацию базы данных
lmbSimpleDb содержит следующие методы для выполнения модификационных запросов:

* **insert($table, $values, $primary_key = 'id')** — вставляет в таблицу $table запись со значениями полей $values. Возвращает значение поля $primary_key вставленной записи, если такое поле не задано в $values.
* **update($table, $values, $criteria = null)** — обновляет записи таблицы $table, которые соответствуют необязательному условию $criteria, на значения полей $values
* **delete($table, $criteria = null)** — удаляет записи из таблицы $table, которые соответствуют необязательному условию $criteria
* **truncateDb()** — очищащает все таблицы базы данных
* **countAffected()** — возвращает количество записей в базе данных, затронуных в результате последнего запроса на модификацию.

Примеры:

    $db->delete('news');
    $db->delete('article', new lmbSQLCriteria('author_id = '. $author_id));
    echo 'Removed ' . $db->countAffected() . ' articles of this author';
 
    $db->insert('user', array('login' => $login, 'password' => $password));
 
    $db->update('user', array('password' => 'other_secret'), 'id = ' . $user_id);

## Прочие методы
lmbSimpleDb также содержит alias-ы методов для управления транзакциями:

* **begin()** — начинает транзакцию
* **commit()** — завершает транзацию
* **rollback()** — откатывает назад транзакцию

А также методы:

* **disconnect()** — отключается от базы данных
* **quote($value)** — применяет родное для базы данных квотирование(quote) строки. Используется для безопасного составления условий.

Например:

    $db = new lmbSimpleDb($conn);
    $rs = $db->select('user', 'login = '. $db->quote($login));
