# Отложенная загрузка некоторых атрибутов. Lazy Attributes
Иногда принимать все поля из таблицы базы данных - это непозволительная роскошь. Например, у нас есть объекты документы (Document), которые в себе содержат поле контент (content), аннотацию, заголовок и т.д. Если нам на определенной странице сайта необходимо показать ссылки на 30 последних документов вместе с аннотацией, тогда зачем нам нужно выбирать также и все поля content? Ведь это приведет к излишнему расходу памяти и трафику между web-сервером и сервером базы данных, даже если мы будем выбирать элементы по одному и если база данных и web-сервер находятся на одной машине.

Для этих случаев lmbActiveRecord поддерживает так называемые «ленивые атрибуты» Lazy Attributes, которые загружаются из базы данных лишь в том случае, когда их реально запросили.

Для определения того, какие поля являются Lazy используется атрибут **$_lazy_attributes**, содержащий список таких полей:

    class Document extends lmbActiveRecord
    {
      protected $_lazy_attributes = array('annotation', 'content');
    }

Теперь если мы создадим объект класса Document, загрузим его из базы, то сразу после загрузки некоторые поля его будут еще недоступны:

    $document = new Document();
    $document->setTitle('a short title');
    $document->setAnnotation('not so long annotation');
    $document->setContent('very long content');
    $document->save();
 
    $loaded_document = new Document($document->getId());
    $object->hasAttribute('news_date'); // Вернет true
    $object->hasAttribute('annotation'); // Вернет false
    $object->hasAttribute('contemt'); // Вернет false
 
    $object->getAnnotation(); // Вернет  not so long annotation
    $object->hasAttribute('annotation'); // Вернет true

## Явное указание загрузки аттрибутов
Чтобы сразу загрузить «ленивые» аттрибуты вместе с другими нужно указать специальную опцию в find методе:

    $obj = lmbActiveRecord::find('Document', array('with_lazy_attributes' => true));
