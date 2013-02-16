# Использование своего подключение к базе данных (connection) при работе с ActiveRecord
Большинство методов класса lmbActiveRecord принимают опциональным параметром объект подключения к базе данных. Мы сознательно в документации опустили этот параметр в описаниях метода, так как необходимость работать с особым подключение, а не с тем, что есть по-умолчанию, появляется относительно редко.

см. также [Подключение к базе данных](../../../../dbal/docs/ru/dbal/connection.md) в разделе документации пакета DBAL.

Итак, объект подключения можно передать:

* **Вторым параметром в конструктор lmbActiveRecord**

        $document = new Document($document_id, $my_connection);
    
При этом объект подключения будет использовать для всех выборок внутри этого объекта, например, передаваться в связанные объекты, загруженные через этот объект, например:

    $author = $document->getAuthor(); // $author будет закружен также при помощи $my_connection.
    
* **в find()-методы последним параметром**

        $documents = lmbActiveRecord :: find('Document', 'section_id = ' . $section_id, $my_connection);
        $document = lmbActiveRecord :: findFirst('Document', array(), $my_connection);
        $sql = 'SELECT document.* FROM document ...';
        $my_documents = lmbActiveRecord :: findBySql('Document', $sql, $my_connection);
        // и т.д.

* **Использовать статический метод setDefaultConnection($conn)**

         lmbActive :: setDefaultConnection($my_connection);
         $document = new Document($document_id); // получит $my_connection

При использовании этого метода все объекты ActiveRecord будут использовать именно это подключение, если им не передано другое в конструктор.
