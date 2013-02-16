# DAO — Data Access lmbObject
## Description
DAO classes are used to unify the process of getting raw data from the persistence layer(it's on the TODO list to rename current DAO implementation into RecordSet).

DAO classes can have two methods:

* **fetch()** that returns **Iterator**
* **fetchById($id)** that returns DataSpace. This method is used in [lmbDataMapper](./lmb_data_mapper.md).

To create a DAO object you can use this code:

    $toolkit = lmbToolkit :: instance();
    $mapper = $toolkit->createDAO('ArticleDAO');

DAO class file will be automatically found with [lmbFileSchema](./lmb_file_schema.md).

## Core DAO classes

Class name | Description
-----------|------------
[TableGatewayDAO](./table_gateway_dao.md) | Allows to make requests to a db table with [lmbTableGateway](../../../dbal/docs/en/dbal/lmb_table_gateway.md)
[QueryBasedDAO](./query_based_dao.md) | Allows to make complex select quesries. QueryBasedDAO is a decorator for [lmbSelectQuery](./lmb_select_query.md).
[ServicesListDAO](./services_list_dao.md) | Returns a list of Services can be used with Limb-based project.
