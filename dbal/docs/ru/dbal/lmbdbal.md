# lmbDBAL
Класс **lmbDBAL** — это фасад для DBAL пакета. При помощи него гораздо эффективнее выполнять запросы к базе данных, чем используя классы [драйвера](./driver.md) напрямую.

## Мновенное выполнение запросов
Самыми базовыми методами класса lmbDBAL можно назвать:

* **fetch($sql, $conn = null)** — выполняет SELECT-запрос. Возвращает набор данных (итератор) в виде объекта, реализующего интерфейс lmbDbRecordSet.
* **execute($sql, $conn = null)** — более общий метод для выполнения sql-запросов. Просто выполняет указанный $sql-запрос.
* **$conn** — это объект подключения. Если этот параметр опущен - lmbDBAL будет использовать объект подключения по-умолчанию. См. страницу {«Подключение к базе данных»](./connection.md).

Например:

    $record_set = lmbDBAL :: fetch('SELECT * FROM article WHERE author_id = '. (int)$author_id);
    lmbDBAL :: execute('DELETE * FROM article WHERE author_id = '. (int)$author_id);

## Фабричные методы для создания объектов lmbSimpleDb и lmbTableGateway
lmbDBAL содержит также 2 фабричных метода, которые могут быть весьма полезны при выполнении простых запросов:

* **db($conn = null)** - возвращает объект класса [lmb_simple_db](./lmb_simple_db.md), который может быть использован для выполнения базовых CRUD-запросов.
* **table($name, $conn = null)** - возвращает объект класса [lmb_table_gateway](./lmb_table_gateway.md), который удобно использовать при работе с одной таблицей базы данных. lmbTableGateway также автоматически делает type casting значений на основе meta-информации, чего пока lmbSimpleDb не делает.

Например:

    lmbDBAL :: db()->insert('user', array('login' => 'vasa', 'password' => 'secret'));
 
    $db = lmbDBAL :: db();
    $db->begin();
    $db->update('user', array('password' => 'other_secret'), 'login = ' . $db->quote($name));
    $db->commit();
 
    lmbDBAL :: table('user')->insert(array('login' => 'vasa', 'password' => 'secret'));

## Фабричные методы для создания объектов lmb*Query
lmbDBAL содержит набор фабричных методов, для создания объектов из подсистемы [Query](./query.md):

* **selectQuery($table, $conn = null)** — создает экземпляр [lmb_select_query](./lmb_select_query.md)
* **updateQuery($table, $conn = null)** — создает экземпляр [lmb_update_query](./lmb_update_query.md)
* **deleteQuery($table, $conn = null)** — создает экземпляр [lmb_delete_query](./lmb_delete_query.md)

Пример1:

    $query = lmbDBAL :: selectQuery('news'); // создали объект класса lmbSelectQuery
    $query->addCriteria(lmbSQLCriteria :: between('date', $date_start, $date_end)); // Применили условие
    $rs = $query->fetch(); // Создали объект record_set, поддерживающий интерфейс lmbDbRecordSet

Пример2:

    $query = lmbDBAL :: deleteQuery('article'); // создали объект класса lmbDeleteQuery
    $query->addCriteria('is_approved = 0'); // Применили условие
    $query->execute(); // Выполнили запрос

## Прочие методы
