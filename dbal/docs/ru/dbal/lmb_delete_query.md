# lmbDeleteQuery
lmbDeleteQuery — при помощи данного класса инкапсулируются SQL-запросы на удаление записей из таблицы базы данных. lmbDeleteQuery является потомоком [lmb_criteria_query](./lmb_criteria_query.md).

lmbDeleteQuery принимает в конструкторе название таблицы, из которой нужно удалить записи. Ниже дан пример использования данного класса (удаление всех записей из таблицы news, дата которых меньше текущей):

    $query = new lmbDeleteQuery('news');
    $query->addCriteria(new lmbSQLFieldCriteria('date_stamp', time(), lmbSQLFieldCriteria :: LESS));
    //можно и так
    $query->addCriteria('date_stamp < ' . time());
 
    $connection = lmbToolkit :: instance()->getDbConnection(); 
    $stmt = $query->getStatement($connection);
    $stmt->execute();
