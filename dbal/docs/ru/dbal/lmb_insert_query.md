# lmbInsertQuery
lmbInsertQuery — при помощи этого класса формируется SQL-запросы для добавления записей в таблицу базы данных. Потомок от [lmbTemplateQuery](./lmb_template_query.md).

Список всех меток расширения:

* **%fields%** — для вставки полей.
* **%values%** — для вставки значений.

Для заполнения этих двух метод есть метод **addField($field, $value)**. Ниже дан пример использования lmbInsertQuery:

    $conn = lmbToolkit :: instance()->getDefaultDbConnection();
 
    $query = new lmbInsertQuery('news', $conn);
    $query->addField('date', $request->get('date'));
    $query->addField('content', $request->get('content'));
    $query->addField('title', $request->get('title'));
 
    $stmt = $query->getStatement();
    $new_record_id = $stmt->insertId('id');
