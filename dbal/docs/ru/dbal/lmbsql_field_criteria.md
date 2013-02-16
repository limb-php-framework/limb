# lmbSQLFieldCriteria
**lmbSQLFieldCriteria** — используется для вставки критерия на поле выборки. Входит в подситему [Criteria](./criteria.md).

lmbSQLFieldCriteria получает в конструкторе название поля, значение и условие, например:

    $criteria = new lmbSQLFieldCriteria('contract.start_date', $date_str, lmbSQLFieldCriteria:: LESS);
    $criteria = new lmbSQLFieldCriteria('contract.end_date', $date_str, '>');

Список доступных условий:

* EQUAL = » = »;
* NOT_EQUAL = » <> »;
* GREATER = » > »;
* LESS = » < »;
* GREATER_EQUAL = » >= »;
* LESS_EQUAL = » < = »;
* LIKE = » LIKE »;
* NOT_LIKE = » NOT LIKE »;
* IN = » IN »;
* NOT_IN = » NOT IN »;
* ISNULL = » IS NULL»;
* ISNOTNULL = » IS NOT NULL»;

Вместо константы вполне можно использовать явные символы.

В классе [lmbSQLCriteria](./lmbsql_criteria.md) есть набор фабричных методов для создания экземляров класса lmbSQLFieldCriteria в более лаконичной форме, например:

    $criteria = lmbSQLCriteria :: equal('is_approved', 1);
