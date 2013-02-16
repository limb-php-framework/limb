# lmbSQLFieldBetweenCriteria
Класс **lmbSQLFieldBetweenCriteria** предназначен для инкапсуляции sql-условия BETWEEN для значения поля таблицы.

Конструктор класса lmbSQLFieldBetweenCriteria принимает название поля, а также 2 значения: левое и правое.

    $criteria = new lmbSQLFieldBetweenCriteria('date', $start_date, $end_date);

В классе lmbSQLCriteria есть статический фабричный метод **between($field, $from, $to)** для создания экземплятов класса lmbSQLFieldBetweenCriteria:

    $criteria = lmbSQLCriteria :: between('date', $start_date, $end_date);

см. также [Использование классов из подсистем Query и Criteria](./intro_to_query_and_criteria.md) и [подсистема Criteria](./criteria.md).
