# MVC pattern
Limb is built around MVC pattern also known as Model2. On the diagram below you can see the core Limb class packages.

## Limb3 structure

* RequestDispatching, Filters, Commands — are the components of Controller.
* DBAL, DAO, ORM, DomainObjects — are the components of Model.

## View
Limb3 uses WACT template engine with additional tags as a View component. Limb supports both Push View and Pull View methods. For example, Limb has `<limb:DAO>` tag that permits you to make requests to database right from a WACT template. Limb also has a number of [View](./view.md) classes that act as mediators between Controller and templates.

## Controller
[Controller](./controller.md) handles application logic. Limb3 Controller consists of the following components:

1. [Intercepting Filters](./filters.md) acting as Front Controller.
2. [Commands](./lmb_command.md) performing domain specific application logic such as data modifications and validation, view selecting, etc.
3. [RequestDispatching](./request_dispatching.md).
4. [Validation](./validation.md).

## Model
[Model](./model.md) deals with business rules and domain logic.

Limb Model consists of the following components:

* [DataContainers](./data_container.md) — classes that hold data. There are two groups of data containers: iterators and datasources.
* [ORM](./orm.md) — lmbObject/Relational Mapping. ORM provides transparent reading and writing of domain objects from/to relational database. ORM makes it possible to create rich domain models where domain objects are unaware of persistence layer. Limb ORM is very simple at the moment.
* [DBAL](./dbal.md) — DataBase Abstraction Layer. Limb DBAL is based on WACT DBAL with some important add-ins.
* [DAO](./daos.md). Clients use DAO classes to fetch raw data from the persistence layer. The term DAO has a bit different meaning in Limb compared to other frameworks. DAO doesn't operate with domain objects, it is more correct to treat Limb DAO as a DataSource(thus it's very likely that in the nearest future DAO will be renamed to RecordSet). DAO classes can be used in templates to query DB right from the template. DAO classes are also used in DataMappers to retrieve information about domain objects.
