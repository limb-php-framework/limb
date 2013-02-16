# Подключение к базе данных
Пакет DBAL предоставляет 2 основных способа получения объекта подключения к базе данных:

* Создание подключения вручную
* Создание подключения по-умолчания на основе конфигурационного файла **settings/db.conf.php**

## Ручное подключение к базе данных
Существует компактный способ создания подключения — при помощи метода **lmbDBAL :: newConnection($dsn)**, где $dsn — это или объект класса lmbDbDSN или просто строка с параметрами подключения, например:

    $connection = lmbDBAL :: newConnection('mysql://root:test@localhost/my_datatase?charset=utf8');
    // или 
    $dsn = new lmbDbDSN('mysql://root:test@localhost/my_datatase?charset=utf8');
    $connection = lmbDBAL :: newConnection($dsn);

Объект подключения к базе данных реализует интерфейс lmbDbConnection. Хотя этот интерфейс содержит методы **connect()** и **disconnect()**, в приложениях явно вызывать эти методы приходится очень редко — это происходит автоматически.

Полученный объект $connection можно каким-либо образом сохранить (например, в toolkit-е) и использовать в различных местах приложения:

На самом деле lmbDBAL делегирует создание connection-а тулкиту, поэтому тоже самое можно записать и другим способом:

    $connection = lmbToolkit :: instance()->createDbConnection('mysql://root:test@localhost/my_datatase?charset=utf8');

## Подключение по-умолчанию
По-умолчанию DBAL настроен таким образом, что подключение создается на основе содержимого файла **db.conf.php**, который находится в папке **settings** проекта. Содержимое db.conf.php описывает параметры соединения с базой данных в виде переменной $conf, которая является массивом с одним параметром — dsn, например:

    <?php
    $conf = array('dsn' => 'mysql://root:test@localhost/my_project?charset=utf8');
    ?>

Для получения соединения по-умолчанию с базой данных можно использовать метод lmbDBAL :: defaultConnection():

    $conn = lmbDBAL :: defaultConnection();

Этот метод, на самом деле, делегирует выполнение тулкиту, а именно методу getDefaultDbConnection(). Поэтому пример, приведённый выше, можно написать иначе:

    $conn = lmbToolkit :: instance()->getDefaultDbConnection();

Метод getDefaultDbConnection появляется в тулките благодаря подключенному в него классу **lmbDbTools**.

Вы можете установить свое собственное подключение по-умолчанию. Для этого можно воспользоваться методами lmbDbTools :: setDefaultDbDSN($conf) или setDefaultDbConnection($conn), например:

    define('MY_DB_PARAMS', 'mysql://root:test@localhost/my_project?charset=utf8');
    [...]
    lmbToolkit :: instance()->setDefaultDbDSN(MY_DB_PARAMS);
    [...]
    $conn = lmbToolkit :: instance()->getDefaultDbConnection();

Подключение по-умолчанию используется везде в DBAL пакете (и в ACTIVE_RECORD к слову), если параметр подключения не указан явно, например:

    lmbDBAL :: db($conn1)->select('news');   // Будет использован $conn1
    lmbDBAL :: db()->select('news'); // Будет использовано подключение по-умолчанию.

## Подключение к нескольким базам данных одновременно
При желании можно подключиться сразу к нескольким различным базам данных одновременно:

    $conn1 = lmbDBAL :: newConnection('mysql://root:test@localhost/my_project?charset=utf8');
    $conn2 = lmbDBAL :: newConnection('pgsql://root:test@localhost/my_other_project?persistent=true&charset=utf8');

Эти объекты разных подключений можно передавать в классы, использующие DBAL-пакет, чтобы они не использовали подключение по-умолчанию, например, в случае с lmbActiveRecord:

    $news = lmbActiveRecord :: find('News', array(), $conn1);
    $article = new Article(array(), $conn2);
    [...]
    $article->save();
