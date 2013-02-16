# Поддержка наследования
Пакет ACTIVE_RECORD поддерживает наследование классов, которые хранятся в рамках одной таблицы. Это значит, что вы можете создать несколько классов, которые будут иметь общего родителя и которые будут храниться в одной таблице базы данных. Эти классы могут иметь различные методы по обработке данных и иметь различные связи. Такой тип наследования называется **Single Table Inheritance**.

## Определение наследуемых классов
Для того, чтобы обеспечить возможности по наследованию, вам необходимо лишь добавить в таблицу, в которой хранятся объекты, одно поле. По-умолчанию это поле называется **kind**. Рассмотрим небольшой пример.

Рассмотрим другой пример из реального приложения. Существуют объекты - тесты (Test), которые содержат вопросы (Question), на которые пользователи должны давать ответы (Answer). Вопросы существуют различных типов:

* вопросы с вариантами ответов (VariantsQuestion),
* вопросы на попадание в заданный диапазон (RangedQuestion),
* вопросы на время (TimeQuestion),
* вопросы на свободный ответ (ExtendedQuestion).

Каждом классу вопросов соответствовал класс ответа, например, TimeAnswer, VariantsAnswer, ExtendedAnswer. В каждом классе ответа реализовывался свой собственный алгоритм подсчета баллов за вопрос. Однако, мы не будем здесь разбирать этот пример полностью, наша цель - показать, какие еще возможности предоставляет пакет ACTIVE_RECORD по работе с наследуемыми объектами.

Итак, у нас были следующие таблицы (наиболее значимые):

    CREATE TABLE `question` (
      `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
      `question` text NOT NULL,
      `min_value` FLOAT NOT NULL,
      `max_value` FLOAT NOT NULL,
      `max_time` INT(11) NOT NULL,
      `test_id` VARCHAR(20),
      `kind` VARCHAR(255),
      PRIMARY KEY  (`id`),
      KEY `kind` (`kind`) 
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
    CREATE TABLE `test` (
      `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
      `title` VARCHAR(255) NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
    CREATE TABLE `answer_variant` (
      `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
      `title` VARCHAR(255) NOT NULL,
      `question_id` BIGINT(20) NOT NULL,
      PRIMARY KEY  (`id`),
    KEY `node_id` (`node_id`) 
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

Наша модель выглядела следующим образом:

    class Question extends lmbActiveRecord
    {
      protected $_db_table_name = 'question';
      protected $_many_belongs_to = array('test' => array('field' => 'test_id',
                                                          'class' => 'Test'));
    }
 
    class TimeQuestion extends Question
    {
      function start(){...}
      function stop(){...}
      function getElapsedTime(){...}
    }
 
    class RangeQuestion extends Question
    {
      function isInRange(){...}
    }
 
    class VariantsQuestion extends Question
    {
      protected $_has_many = array('variants' => array('field' => 'question_id',
                                                       'class' => 'AnswerVariant'));
    }
 
    class ExtendedQuestion extends Question
    {
    }

    class Test extends lmbActiveRecord
    {
      protected $_has_many = array('questions' => array('field' => 'test_id',
                                                        'class' => 'Question'));
    }

Обратите внимание на определение $_db_table_name в классе Question — это сделано для того, чтобы все дочерние классы «знали», в какую таблицу они должны записываться и не пытались угадать название таблицы самостоятельно.

Отметим, что делать Question абстрактным классом нельзя — таковы особенности реализации класса lmbActiveRecord.

Еще раз — поле **kind** в таблице question требуется для того, чтобы lmbActiveRecord знал, что объекты, хранимые в данной таблице могут быть различных родственных классов.

До релиза пакета ACTIVE_RECORD 0.2.2 (включительно) для указания базового класса применялся атрибут $_base_class, который указывал lmbActiveRecord, какой класс является базовым в иерархии связанных объектов. Начиная с более поздней версии этот атрибут уже не используется, так как lmbActiveRecord определяет базовый класс самостоятельно.

Начиная с версии ACTIVE_RECORD 0.3.0 в kind хранится полный путь наследования объекта в виде **Foo|Bar|Zoo|..** — и это позволяет правильно получать все дочерние объекты какого-то родительского класса. Поиск ведется по шаблону LIKE class_path%, то есть для запроса lmbActiveRecord :: find('Question'); сформируется условие WHERE kind LIKE 'Question|%'.

## Работа с наследуемыми классами
Теперь можно попробовать создать экземпляры новых классов.

Создадим тест с четырьмя различными вопросами:

    $test = new Test();
    $test->setTitle('Super test');
    $test->save();
 
    $question1 = new VariantsQuestion();
    $question1->setQuestion('Choose a variant ...');
    $question1->addToVariants($variant1);
    $question1->addToVariants($variant2);
 
    $question2 = new RangeQuestion();
    $question2->setQuestion('Give a number...');
    $question2->setMinValue(10);
    $question2->setMaxValue(20);
 
    $question3 = new TimeQuestion();
    $question3->setQuestion('Do something in time ...');
    $question3->setMaxTime(1000);
 
    $question4 = new ExtendedQuestion();
    $question4->setQuestion('Tell us something...');
 
    $test->addToQuestions($question1);
    $test->addToQuestions($question2);
    $test->addToQuestions($question3);
    $test->addToQuestions($question4);
    $test->save();

Для загрузки объектов можно указывать базовый класс, приведение к нужному классу будет выполнено автоматически.

    $variants_question = lmbActiveRecord :: findById('Question', $question_id1);
    echo "Variants question with variants :\n";
    foreach($variants_question->getVariants as $variants)
      echo $operator->getSection()->getTitle() . "\n"
    [...]
    $time_question = lmbActiveRecord :: findById('Question', $question_id3);
    $time_question->start();
    // [...]
    $time_question->stop();
    echo "You spent " . $time_question->getElapsedTime() . " seconds";

Если же нужно получить только объекты специального класса, то его название нужно использовать в find()-методе:

    $time_questions = lmbActiveRecord :: findById('TimeQuestion');

Допустим нам необходимо получить в тесте только вопросы на время (это было одним из функциональных требований). Для этого мы можем задать свойство **class** в массиве $params, который приходит в метод find($params = array()) коллекции. Подробнее о методе find() в разделе [«Дополнительная информация по отношениям»](./more_on_relations.md).

Например:

    $test = new Test($test_id);
    $time_questions = $test->getQuestions()->find(array('class' => 'TimeQuestion'));

## Смена поля для хранения информации о классах
Вы можете сменить название поля, в котором у вас хранится информация о том, объект какого именно класса хранится в записи таблицы. Для этого вызовите статический метод lmbActiveRecord :: **setInheritanceField($new_field_name)**.

Получить текущее имя можно при помощи статического метода lmbActiveRecord :: **getInheritanceField()**.
