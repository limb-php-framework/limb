# lmbUpdateQuery
lmbUpdateQuery — класс, инкапсулирующий запросы по обновлению записей таблицы базы данных. Потомок от [lmb_criteria_query](./lmb_criteria_query.md). Принимает в конструкторе название таблицы, записи в которой нужно обновить.

Список всех меток расширения:

* **%fields%** — для вставки полей и значений этих полей.
* **%where%** — для вставки условий.

Для заполнения %fields% есть методы:

* **addField($field, $value)**.
* **field($field, $value)** — алиас для addField()
* **addRawField($field)**.
* **rawField($field)** — алиас для addRawField()

Для заполнения %where% есть методы:

* **addCriteria($criteria)**
* **where($criteria)** — алиас для addCriteria($criteria)

Ниже дан пример использования lmbUpdateQuery:

    $query = new lmbUpdateQuery('news');
    $query->addField('date', $request->get('date'));
    $query->addField('content', $request->get('content'));
    $query->addField('title', $request->get('title'));
    $query->addRawField('changed_times = changed_times + 1');
    $query->addCriteria(new lmbSQLFieldCriteria('id', $id));
 
    $conn = lmbToolkit :: instance()->getDbConnection();
    $stmt = $query->getStatement($conn);
    $stmt->execute();

или короче:

    $query = lmbDBAL :: updateQuery('news');
    $query->field('date', $request->get('date'))->field('content', $request->get('content'))->field('title', $request->get('title')); 
    $query->rawField('changed_times = changed_times + 1');
    $query->where(''id = '. (int) $id'); 
    $query->execute();

см. также [Использование классов из подсистем Query и Criteria](./intro_to_query_and_criteria.md)
