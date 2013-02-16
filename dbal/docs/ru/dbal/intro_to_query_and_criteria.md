# Использование классов из подсистем Query и Criteria
## Использование Query
Классы из [подсистемы Query](./query.md) инкапсулируют шаблон sql-запроса и предоставляют средства для расширения (заполнения) этого шаблона. Поясним на примере.

Возьмем класс [lmbSelectQuery](./lmb_select_query.md):

    $connection = ...
    $query = new lmbSelectQuery('news', $connection);
    
Если $connection не передать (это опциональный параметр), тогда будет использовано подключение к базе данных по-умолчанию. (см. [Подключение к базе данных](./connection.md)).

Этот $query пока аналог запроса «SELECT * FROM news». Попробуем его расширить:

    $query->addTable('comments');
    $query->addField('comments.*');
    $query->addCriteria('date < ' . time() - 60*60*24);
    $query->addCriteria('comments.news_id = news.id');

Так как класс [lmbSelectQuery](./lmb_select_query.md) поддерживает chaining, а также есть набор алиасов на базовые методы, этот пример можно записать намного компактнее:

    $query->from('comments')->field('comments.*')->where('news.date > ' . time() - 60*60*24)->where('comments.news_id = news.id');

Теперь это уже аналог запроса «SELECT comments.* FROM news, comments WHERE news.date < 123456567 AND comments.news_id = news.id». При добавлении нескольких условий при помощи метода where(), они объединяются через AND. см. «логические операции с criteria» ниже для добавления более сложных условий.

После произведения модификаций над query можно выполнить ее, получив record_set. Для lmbSelectQuery используется метод **getRecordSet()** или **fetch()**;

    $rs = $query->getRecordSet();
    $rs = $query->fetch();//тоже самое

Для классов [lmb_delete_query](./lmb_delete_query.md) и [lmb_update_query](./lmb_update_query.md) для выполнения запроса используется метод **execute()**:

    $query = new lmbDeleteQuery('news');
    $query->execute();

Также из $query можно получить объект утверждения (statement) при помощи метода **getStatement()**, например:

    $statement = $query->getStatement();
    $rs = $statement->getRecordSet()

Или можно получить готовый sql-код при помощи метода **toString()**:

    $sql = $query->toString();

Для создания объекта query в классе [lmbDBAL](./lmbdbal.md) есть статические фабричные методы, например:

    $rs = lmbDBAL :: selectQuery('news')->order('date')->fetch()->paginate(0, 10);
    lmbDBAL :: deleteQuery('news')->where('date < ' time() . 24*60*68*365)->execute();

## Когда лучше использовать Query классы
Мы предпочитаем использование явного $sql-кода, во всех случаях, когда возможно обойтись без query.

Например, следующий запрос:

    $sql = "SELECT comments.* FROM news, comments WHERE news.date < 123456567 AND comments.news_id = news.id";
    $rs = lmbDBAL :: fetch($sql);

…можно было бы, конечно, написать и так:

    $query = new lmbSelectQuery('news'); 
    $query->from('comments')->field('comments.*')->where('news.date > ' . time() - 60*60*24)->where('comments.news_id = news.id');
    return $query->getRecordSet();

Однако излишнее усложнение обычно ни к чему. Использование объектного query полезно в случаях, когда ограничения в запрос могут добавлять набором методов или классов, когда от наличия тех или иных полей в выборках зависит от каких-то внешних данных.

## Использование критерий Criteria
### Создание критерий
Критерии используются обычно совместно с потомками [lmb_criteria_query](./lmb_criteria_query.md), такими как [lmb_select_query](./lmb_select_query.md), [lmb_update_query](./lmb_update_query.md) и [lmb_delete_query](./lmb_delete_query.md) для добавления условий к %where% и %having%.

Самый простой способ создать объект criteria — это создать экземпляр класса [lmbSQLCriteria](./lmbsql_criteria.md), который принимает просто часть sql-кода и набор значений для замены в placeholder-ах, например:

    $criteria = new lmbSQLCriteria('news_id = ' . (int)$news_id);
    // пример с placeholder-ами. 
    $criteria = new lmbSQLCriteria('news_id = ? AND section_id = ?', array((int)$news_id, (int)$section_id));
    // или
    $criteria = new lmbSQLCriteria('news_id = :news_id: AND section_id = :section_id:', 
                                   array('news_id' => (int)$news_id, 'section_id' => (int)$section_id));

Часто используется также класс [lmbSQLFieldCriteria](./lmbsql_field_criteria.md), который позволяет вставлять условия на какие-либо поля выборок, например:

    lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
 
    $criteria1 = new lmbSQLFieldCriteria('news_id', (int)$news_id);
    // или
    $criteria1 = new lmbSQLFieldCriteria('news_id', (int)$news_id, lmbSQLFieldCriteria :: EQUAL);
    // или
    $criteria1 = new lmbSQLFieldCriteria('news_id', (int)$news_id, ' = ');
 
    $criteria2 = new lmbSQLFieldCriteria('name', '%' . $name . '%', lmbSQLFieldCriteria :: LIKE);
    // или
    $criteria2 = new lmbSQLFieldCriteria('name', '%' . $name . '%', ' LIKE ');

Полный список условий см. в [описании класса lmbSQLFieldCriteria](./lmbsql_field_criteria.md).

В классе [lmbSQLCriteria](./lmbsql_criteria.md) для есть набор статических фабричных методов для удобного создания различных объектов класса lmbSQLFieldCriteria, например:

    $criteria1 = lmbSQLCrireria :: equal('news_id', (int)$news_id);
    $criteria2 = lmbSQLCrireria :: like('name', '%' . $name . '%');
    $criteria3 = lmbSQLCrireria :: in('section_id', array($section_id1, $section_id2));

Остальные методы см. в описании класса [lmbSQLCriteria](./lmbsql_criteria.md).

Для between условий есть класс [lmbSQLFieldBetweenCriteria](./lmbsql_field_between_criteria.md):

    lmb_require('limb/dbal/src/criteria/lmbSQLFieldBetweenCriteria.class.php');
 
    $criteria = new lmbSQLFieldBetweenCriteria('date', $date_start, $date_end);

В классе [lmbSQLCriteria](./lmbsql_criteria.md) для удобства есть также статический фабричный метод для создания объекта класса lmbSQLFieldBetweenCriteria, например:

    $criteria1 = lmbSQLCrireria :: between('date', $date_start, $date_end);

### Логические операции с критериями
Criteria классы поддерживают логические операции:

* **and**
* **or**
* **not**

Поддержка логических операций реализована в базовом класса lmbSQLBaseCriteria поэтому их поддерживают все критерия.

Для **объединения (AND)** условий, используется метод **addAnd($criteria)**, например:

    $criteria = new lmbSQLFieldCriteria('ingredient.name', '%'.$name.'%', lmbSQLFieldCriteria :: LIKE);
    $new_criteria = new SQLFielBetweenCriteria('value', 10, 20)
    $criteria->addAnd($new_criteria);

Для метода addAnd() есть алиас **add($criteria)**, поэтому в более краткой форме пример выше можно записать следующим образом:

    $criteria = lmbSQLField :: like('ingredient.name', '%'.$name.'%')->add(lmbSQLField :: between('value', 10, 20));

Для вставки условий **или OR** есть метод **addOr($criteria)**:

    $criteria = lmbSQLField :: equal('name', 'Vasa')->addOr(lmbSQLField :: equal('name', 'Ivan'));

Для вставки **отрицания (NOT)** самой критерии есть метод **not()**:

    $criteria = lmbSQLField :: equal('name', 'Vasa')->not(); // Даст в результате NOT(name = "Vasa")

Также есть метод notAll() который применяет к общему критерию, из которых состоит данное, например:

    $a = new lmbSQLCriteria('a');
    $b = new lmbSQLCriteria('b');
    $a->add($b);
    $a->notAll(); // В результе получим NOT(a AND b)

Методы логических операций поддерживают **chaining**:

    $a = new lmbSQLCriteria('a');
    $b = new lmbSQLCriteria('b');
    $a->notAll()->add($b);

Можно и посложнее:

    $a = new lmbSQLCriteria('a');
    $b = new lmbSQLCriteria('b');
    $c = new lmbSQLCriteria('c');
    $d = new lmbSQLCriteria('d');
    $e = new lmbSQLCriteria('e');
    $g = new lmbSQLCriteria('g');
    $h = new lmbSQLCriteria('h');
    $a->addOr($b->addAnd($c))->addAnd($d->addOr($e)->addAnd($g->addOr($h)));
    // Получим - a OR (b AND c) AND (d OR e AND (g OR h))
