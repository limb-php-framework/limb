# Criteria
**Criteria** — это набор классов, которые используются для инкапсуляции условий в SQL запросах в конструкциях WHERE и HAVING. см. страницу [Использование классов из подсистем Query и Criteria](./intro_to_query_and_criteria.md).

## Диаграмма классов
![Alt-Диаграмма классов](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:packages:dbal:limb3_dbal_criteria.png)

Все критерии наследуются от базового lmbSQLBaseCriteria, которые реализует необходимую функциональность логических операций (AND и OR) и по парсингу критерий.

## Список классов
Критерий | Назначение
---------|-----------
[lmbSQLCriteria](./lmbsql_criteria.md) | Класс-фасад подсистемы Criteria. Позволяет также вставлять условия «как есть (as is)«
[lmbSQLFieldCriteria](./lmbsql_field_criteria.md)	| Позволяет вставлять условия на какое-либо поле выборки.
[lmbSQLFielBetweenCriteria](./lmbsql_field_between_criteria.md)	| Позволяет вставлять BETWEEN условия для полей выборки.
