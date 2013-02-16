# DBAL Driver
DBAL Driver — базовые классы для работы с базами данных, на основе которых построена остальнаях часть пакета DBAL.

Условно driver можно разделить на 2 составляющие:

* выполнение запросов к базе данных
* получение meta-информации о базе данных (хотя это тоже подразумевает под собой выполнение некоторых запросов)

Здесь мы рассмотрим архитектуру DBAL более подробно.

## Классы и интерфейсы, отвечающие за выполнение запросов к базе данных
Если вы прочитали [«Введение в пакет DBAL»](./intro.md), то уже знаете, что основными понятиями здесь являются

* подключение (connection)
* утверждения (statements)
* наборы данных (record sets)

### Выполнение SELECT-запросов
Рассмотрим, как выглядит статическая диаграмма классов, связанных с выполнением SELECT-запросов ( на примере MySql-драйвера):

![Alt-Выполнение SELECT-запросов](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:packages:dbal:limb3_dbal_query_statements.png)

Интерфейс **lmbDbConnection**, который реализует класс **lmbMysqlConnection** содержит метод newStatement($sql), который на базе sql-кода создает объект утверждения (statement) того или иного типа. При наличии ключевого слова SELECT, lmbMysqlConnection создает объект класса **lmbMysqlQueryStatement**.

    lmb_require('limb/dbal/src/drivers/mysql/lmbMysqlConnection.class.php');
 
    $config = array('host' => 'localhost',
                    'user' => 'root',
                    'password' => 'secret',
                    'database' => 'my_datatase',
                    'charset' => 'utf8');
 
    $connection = new lmbMysqlConnection($config);
 
    $sql = 'SELECT * FROM news';
    $statement = $connection->newStatement($sql); // возвратит объект класса lmbMysqlQueryStatement

Объект $statement класса lmnMysqlQueryStatement реализует интерфейс **lmbDbQueryStatement** и содержит ссылку на объект подключения класса lmbMysqlConnection.

Создание объекта $statement еще не значит выполнение реального запроса к базе данных. Это происходит в DBAL как можно позже. До выполнения реального запроса можно произвести некоторые модификации, особенно это актуально при работе с наборами данных (см. ниже)

Из $statement можно получить различные данные:

* **getRecordSet()** — возвращает весь набор данных в виде итератора. Итератор поддерживает интерфейс lmbDbRecordSet.
* **getOneRecord()** — возвращает первую запись из набора данных в виде объекта класса lmbMysqlRecord, который реализует интерфейс lmbDbRecord. Выполняет реальный запрос.
* **getOneValue()** — возвращает первое значение из записи, полученной в результате выполнения запроса. Актуально для «SELECT count(*) as numb_of FROM …» запросов. Выполняет реальный запрос.

Самым интересным для нас является метод lmbDbQueryStatement :: getRecordSet(), который возвращает итератор с набором данных. По сути getRecordSet() возвращает курсор на результат запроса - это значит, что данные в память загружаются только по мере прохождения по итератору, а не сразу все.

Итак, lmbDbQueryStatement :: getRecordSet() возвращает объект, который реализует интерфейс lmbDbRecordSet. В нашем случае это будет объект класса **lmbMysqlRecordSet**.

lmbDbRecordSet является расширением для интерфейса [lmbCollectionInterface](../../../../core/docs/ru/core/lmb_collection_interface.md), это значит, что набор данных должен поддерживать:

* стандартную работу через методы интерфейса **Iterator** (rewind, valid, next, current, key)
* ограничение размера выборки при помощи метода **paginate($offset, $limit)**
* сортировку при помощи метода **sort($sort_params = array())**
* получение количества элементов в выборке при помощи метода **count()**

Пример:

    [...]
       $statement = $connection->newStatement($sql); // возвратит объект класса lmbMysqlQueryStatement
       $rs = $statement->getRecordSet();
       echo $rs->count(); // выведет количество элементов в выборке. count() приводит к выполнению отдельного запроса к базе данных.
       $rs->sort(array('title' => 'ASC'));
       $rs->paginate(0, 10);
 
       for($rs->rewind(); $rs->valid(); $rs->next())
       {
         $record = $rs->current();
         echo $record['title'] . "\n";
       }

Обратите внимание, методы **at($pos)** и **count()** интерфейса lmbDbRecordSet приводят к выполнению отдельных запросов к базе данных, отличных от «основного» запроса record_set-а.

Реальный запрос к базе данных при работе с record_set-ом происходит при вызове метода rewind(), явно или неявно(например, при использовании конструкции foreach).

Метод current() record_set-а возвращает объект, реализующий интерфейс lmbDbRecord (в нашем случае это объект класса lmbMysqlRecord), который в свою очередь является расширением интерфейса [lmbSetInterface](../../../../core/docs/ru/core/lmb_set_interface.md).

### Выполнение запросов на модификацию
Рассмотрим, как выглядит статическая диаграмма классов, связанных с выполнением запросов, связанных с модификацией базы данных(на примере MySql-драйвера):

![Alt-Выполнение запросов на модификацию](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:packages:dbal:limb3_dbal_modify_statements.png)

Эти запросы отличаются тем, что на запросы DELETE,UPDATE и INSERT, создаются объекты, реализующие интерфейсы lmbDbManipulationStatement и lmbDbInsertStatement.

Основным методом для выполнения утверждения является метод lmbDbStatement :: **execute()**.

Интерфейс lmbDbManipulationStatement также содержит метод **getAffectedRowsCount()** который возвращает количество записей в базе данных, затронутых выполнением запроса.

    $sql = "DELETE * FROM founding_fathers WHERE last_name LIKE :last_name:%";
    $stmt = $connection->newStatement($sql);
    $stmt->setVarChar('last_name', 'Iva');
    $stmt->execute(); // выполнит запрос.
    echo 'Removed ' . $stmt->getAffectedRowCount() . ' rows';

Конструкции вида :last_name: — это placeholder-ы для значений. Интейрфейс lmbDbStatement содержит различные методы для установки значений в placeholder-ы, type-cast и escape значений:

* **setInteger($name, $value);**
* **setChar($name, $value);**
* **setFloat($name, $value);**
* и другие.

Есть более базовый метод lmbDbStatement :: **set($name, $value)**, который пытается самостоятельно угадать тип переданного значения.

Интерфейс lmbDbInsertStatement содержит метод **insertId($field_name = 'id')**, который является аналогом execute(), однако возвращает значение поля $field_name только что вставленной записи. Этот метод полезен, например, если первичный ключ является автоинкрементным.

## Подсистема получения meta-информации
Driver также содержит средства для получения meta-информации о базе данных.

Статическая диаграмма классов выглядит следующим образом:

![Alt-Статическая диаграмма классов](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:packages:dbal:limb3_dbal_meta.png)

Мы не будем заострять внимание на этой подсистеме, и подробно ее описывать. Meta-подсистема активно используется в классе [lmbTableGateway](./lmb_table_gateway.md) и в пакете [ACTIVE_RECORD](../../../..//active_record/docs/ru/active_record.md).

Получение информации о структу базы данных занимает некоторое время, поэтому для ускорения работы эти информацию лучше кешировать. Чтобы включить эту возможность нужно определить значение константы **LIMB_CACHE_DB_META_IN_FILE** как true, где-то в setup.php файле приложения, например:

    @define('LIMB_CACHE_DB_META_IN_FILE', true);
