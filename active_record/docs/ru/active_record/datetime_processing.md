# Обработка даты в объектах Active Record
В примерах CRUD и shop считалось, что дата для объектов модели при их создании берется из формы. Не очень-то эффективно, правда? Можно автоматизировать этот процесс. Причем сделать это максимально универсально.

## Постановка задачи
Итак, предположим, что мы создаем на основе Limb3 целый портал. Согласитесь, дата используется повсеместно - от записи в ленте новостей и картинок в фотогалерее до записи в форуме и гостевой книге. Неплохо также предоставить конечным пользователям (администраторам) вашего портала изменять формат даты для каждого из классов, например в гостевой книге можно выводить дату с точностью до дня (xx-xx-xxxx), на форуме нужно с точностью до секунды (xx-xx-xxxx xx:xx:xx). Если вашу систему будут использовать в Америке, наверняка придется поставить странный для нас формат yyyy-dd-mm, ну и так далее. В базе данных же удобно хранить дату в поле типа DATETIME либо как TIMESTAMP, или просто INT (в виде UNIX timestamp). Мы условимся хранить дату в поле DATETIME, т.е. в формате yyyy-mm-dd hh:mm:ss.

## Решение задачи
Начнем с создания общего класса для всех AR, которым нужна дата. А сами классы модели будем наследовать от этого класса. Для представления в модели поля date используем не обычное поле active record, а [Value Object](./value_objects.md).

    lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
    lmb_require('src/model/appDate.class.php');
 
    class appActiveRecordWithDate extends lmbActiveRecord
    {
 
      protected $_composed_of = array('date' => array('field' => 'date',
                                                      'class' => 'appDate',
                                                      'getter' => 'getForDatabase'));
 
      function _onBeforeSave()
      {
        if(!$this->getDate())
          $this->setDate(new appDate());
      }
 
    }

Что мы этим определили? Во всех потомках класса appActiveRecordWithDate теперь будет Value Object «date», который будет связан с одноименным полем в таблице БД этого класса и будет сохраняться там с помощью метода getForDatabase класса appDate. При создании объекта (событие _onBeforeSave() с неопределенным поле $this→get('date')) установим новое значение.

    lmb_require('limb/datetime/src/lmbDate.class.php');
 
    class appDate extends lmbDate
    {
        const TO_MINUTES = "%02d-%02d-%04d %02d:%02d";
        const FOR_DB = "%04d-%02d-%02d %02d:%02d:%02d";
 
        /**
         * @return $date formatted dd-mm-yyyy hh:mm
         */
        public function getToMinutes()
        {
           return sprintf(self::TO_MINUTES,
                           $this->getDay(), $this->getMonth(), $this->getYear(),
                           $this->getHour(), $this->getMinute()
            );
       }
 
       /**
        * @return $date formatted yyyy-mm-dd hh:mm:ss
        */
       public function getForDatabase()
       {
           return sprintf(self::FOR_DB,
                          $this->getYear(), $this->getMonth(), $this->getDay(),
                          $this->getHour(), $this->getMinute(), $this->getSecond()
           );
       }
    }

Итак, мы сделали класс appDate. Благодаря тому, что он наследует класс lmbDate из пакета DATETIME, он обладает следующими свойствами:

1. умеет в конструкторе определять все «параметры даты-времени»: год, минута, секунда, etc, из текущего времени (или не текущего, там очень гибкий конструктор).
2. имеет generic интерфейс, т.е. геттеры и сеттеры для всех этих «параметров»: getHour(), getMinute(), etc., благодаря чему его можно использовать как обычно поле ActiveRecord.

Подробнее об этом классе. В начале мы определили две константы - маски отображения времени для базы данных и «с точностью до минуты». Далее имеются два метода getToMinutes и getForDatabase. Последний мы уже встречали, когда изучали описание value object, он используется при создании нового объекта в базе данных. Первый можно использовать, например, в шаблоне. Понятно, что таких методов (они должны обязательно начинаться с get..()) можно сделать сколько угодно на любой вкус. Теперь мы создаем класс новости

    lmb_require('src/model/appDate.class.php');
    lmb_require('src/model/appNewsappActiveRecordWithDate.class.php');
    class appNews extends appActiveRecordWithDate
    {
    ...
    }

Он будет автоматически поддерживать все манипуляции с полем date.

Тогда в шаблоне, например, news/display.html можно писать

    <active_record:fetch using='src/model/appNews' target='news'/>
    <list:list id='news'>
      <list:item>
        Текст: {$text}<br/>
        Дата: {$date.to_minutes}<br/>
        Автор: {$author}
      </list:item>
    </list:list>

Как же отобразится дата? при обращении к полю $date благодаря использованию value object произойдет обращение к классу appDate (мы именно его указали при описании value object), который будет инстанциирован с параметром — полем date из БД. А использование **$date.to_minutes** приведет к вызову generic геттера **appDate::getToMinutes()** (иными словами обращение $date→get('to_minutes') приведет к вызову метода $date→getToMinutes()).

## Выводы
Итак, мы решили сразу несколько проблем:

* Создали единую систему хранения и форматирования даты для всей системы. Теперь достаточно отнаследовать наши классы от appActiveRecordWithDate, и вся логика по работе с датой будет доступна.
* Создание нового, даже самого заковыристого, формата отображения даты сводится лишь к созданию нового геттера в классе appDate.
* Если в каком-то классе модели нужно несколько дат (так бывает в порталах часто: дата создания, дата редактирования, дата последнего доступа, etc), достаточно по аналогии добавить описание value object'ов, и все будет работать. Если таких классов много, то у них тоже можно выделить общего предка.
