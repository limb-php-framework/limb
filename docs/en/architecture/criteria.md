# Criteria
Criteria is a group of classes that allows you to build complex conditions for SQL queries using nice OO interface. The initial conception was adopted from [Propel](http://propelorm.org/) and later was a bit simplified.

## UML Static Structure

![Alt-Диаграмма классов](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:packages:dbal:limb3_dbal_criteria.png)

### Criteria classes
All criteria classes are children of lmbSQLBaseCriteria class that implements basic functionality to allow a composition of criteria.


Class name | Description
-----------|------------
[lmbSQLRawCriteria](../../../dbal/docs/en/dbal/lmbsql_raw_criteria.md) | Allows to insert a condition into SQL query as is.
[lmbSQLFieldCriteria](../../../dbal/docs/en/dbal/lmbsql_field_criteria.md) | Allows to insert a condition on some field.
[lmbSQLTableFieldCriteria](../../../dbal/docs/en/dbal/lmbsql_table_field_criteria.md) | A child class of lmbSQLFieldCriteria. Requeres explicit table name.
[SQLFielBetweenCriteria](../../../dbal/docs/en/dbal/lmbsql_field_between_criteria.md) | Allows to insert between conditions into SQL query.
[lmbSQLFalseCriteria](../../../dbal/docs/en/dbal/lmbsql_false_criteria.md) | Critetia that is never passed.
[lmbSQLTrueCriteria](../../../dbal/docs/en/dbal/lmbsql_true_criteria.md) | Criteria that is always passed.

### Criteria usage
Criteria are commonly used with [lmbSelectQuery](../../../dbal/docs/en/dbal/lmb_select_query.md) class to insert conditions into %where% and %having% placeholders.

Here is how [lmbSQLFieldCriteria](../../../dbal/docs/en/dbal/lmbsql_field_criteria.md) is used:

    $query->addCondition(newSQLFieldCriteria('name', '%'.$name.'%', lmbSQLFieldCriteria :: LIKE));

It's possible to combine criteria objects:

    $criteria = new lmbSQLTableFieldCriteria('ingredient.name', '%'.$name.'%', lmbSQLFieldCriteria :: LIKE);
    $new_criteria = new lmbSQLTableFieldCriteria('ingredient.name', '%'.$name.'%', lmbSQLFieldCriteria :: LIKE);
    $criteria->addOr($new_criteria);
    $criteria->addAnd(new SQLFielBetweenCriteria('value', 10, 20));
    $query->addCondition($criteria);
