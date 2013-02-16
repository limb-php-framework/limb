# lmbSQLCriteria
Класс **lmbSQLCriteria** инкапсулирует условие для sql-запроса «как есть as is». Также lmbSQLCriteria содержит набор статических фабричных методов для создания экземплятор класса [lmbSQLFieldCriteria](./lmbsql_field_criteria.md) и [lmbSQLFieldBetweenCriteria](./lmbsql_field_between_criteria.md). lmbSQLCriteria наследуется от абстрактного класса lmbSQLBaseCriteria.

Пример:

    $criteria = new lmbSQLCriteria('is_approved = 1');

Список статических фабричных методов:

* **objectify($args)** — делает объект класса lmbSQLCriteria из $args, если $args является строкой.
* **create($raw_sql = '1 = 1', $values = array())** — возвращает объект класса lmbSQLCriteria. Аналог вызова конструктора. Используется для chain-операций, так как php-не поддерживает chaining после вызова конструктора.
* **between($column, $value_from, $value_to)** — возвращает экземпляр класса lmbSQLFieldBetweenCriteria.
* **in($column, $values)** — аналог «new lmbSQLFieldCriteria($column, $values, lmbSQLFieldCriteria :: IN)»
* **equal($column, $value)** — аналог «new lmbSQLFieldCriteria($column, $value)»
* **like($column, $value)** — аналог «new lmbSQLFieldCriteria($column, $value, lmbSQLFieldCriteria :: LIKE)»
* **isNull($column)** — аналог «new lmbSQLFieldCriteria($column, null, lmbSQLFieldCriteria :: IS_NULL)»

Пример:

    $criteria = lmbSQLCriteria :: objectify('is_appoved = 1');
    $criteria->addAnd(lmbSQLCriteria :: between('date', $date_start, $date_end));

Пример цепочного вызова(chaining):

    $criteria = lmbSQLCriteria :: create('is_appoved = 1')->addAnd(lmbSQLCriteria :: between('date', $date_start, $date_end));

см. также [Использование классов из подсистем Query и Criteria](./intro_to_query_and_criteria.md) и [подсистема Criteria](./criteria.md).
