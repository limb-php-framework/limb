# lmbSelectQuery
**lmbSelectQuery** — при помощи этого класса формируется SQL-запросы для выборки данных из базы данных. Потомок от [lmbSelectRawQuery](./lmb_select_raw_query.md).

## Пример использования lmbSelectQuery

    $query = new lmbSelectQuery('news');
    $rs = $query->field('title', 't')->
          join('news', 'athour_id', 'author', 'id')->
          order('news.id', 'desc')->
          group('date')->
          having('date < ' . time())->
          where('author_id=2')->
          fetch(); 
