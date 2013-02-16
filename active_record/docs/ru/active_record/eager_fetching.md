# Жадная загрузка или Eager fetching
Начиная с релиза 2008.1 (доступно также в SVN-версии), пакет ACTIVE_RECORD поддерживает так называемую «жадную загрузку» или eager-fetching.

Сразу же сделаем небольшое отступление: Eager fetching в ACTIVE_RECORD реализован совершенно иным образом, чем, например, Doctrine Query Language. Eager fetching в ACTIVE_RECORD — намного более простой механизм, он не ставил перед собой цель сделать некий аналог SQL-языка с учитыванием отношений между классами модели.

## Введение в eager fetching

Жадная загрузка — это процесс выполнения операций по загрузке связанных объектов ActiveRecord из базы данных с использованием минимального количества запросов. Рассмотрим eager fetching для начала на простом примере, а затем расскажем о всех возможностях eager fetching, реализованных в пакете lmbActiveRecord.

Допустим, у нас есть след. доменная модель: Программы содержат Курсы, Курсы содержат Лекции:

    class Program extends lmbActiveRecord
    {
      protected $_has_many = array('courses' => array('field' => 'program_id',
                                                      'class' => 'Course'));
    }
 
    class Course extends lmbActiveRecord
    {
      protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                   'class' => 'Lecture'));
 
      protected $_many_belongs_to = array('program' => array('field' => 'program_id',
                                                             'class' => 'Program',
                                                             'can_be_null' => true));
    }
 
    class Lecture extends lmbActiveRecord
    {
      protected $_many_belongs_to = array('course' => array('field' => 'course_id',
                                                            'class' => 'Course'),
                                          'alt_course' => array('field' => 'alt_course_id',
                                                                'class' => 'Course',
                                                                'can_be_null' => true));
    }

Допустим у нас есть 2 курса по 5 лекций. Тогда вывод полного списка всех лекций с указанием курса, к которому каждая лекция принадлежит будет выгдять следующим образом:

    $lectures = lmbActiveRecord :: find('Lecture');
      foreach($lectures as $lecture)
        echo 'Lecture '$lecture->getTitle() . ' of ' . $lecture->getCourse()->getTitle() . " course.\n";

Проблема с этим кодом в том, что для подобного вывода потребуется сделать 6 запросов к базе данных: 1 для лекций и еще 5 для курсов (или как минимум 3, если бы мы кешировали загруженные курсы)

Eager fetching дает возможность загрузить все эти данные при помощи 1 запроса:

    $lectures = lmbActiveRecord :: find('Lecture', array('join' => 'course'));
    foreach($lectures as $lecture)
      echo 'Lecture '$lecture->getTitle() . ' of ' . $lecture->getCourse()->getTitle() . " course.\n";

Обратите внимание на использование параметра **join**, а качестве значения использовалось название отношения, то есть course. Использование join-а автоматически приведет к тому, что запрос к таблице lecture будет дополнен LEFT JOIN course. Отметим сразу, что join можно использовать для отношений, когда с базовым объектом связан только 1 дополнительный объект, то есть для отношений has_one, many_belongs_to и belongs_to. В других случаях используется параметр **attach** (см. ниже).

Теперь рассмотрим обратную ситуацию, нужно вывести курсы с указанием списка лекций по каждому курсу:

    $courses = lmbActiveRecord :: find('Course');
    foreach($courses as $course)
    {
      echo 'Course ' . $course->getTitle() . ": \n";
      foreach($course->getLectures() as $lecture)
        echo '  Lecture ' . $lecture->getTitle() . ".\n";
    }

Здесь проблема аналогична, для каждого курса мы будем выполнять запрос на список его лекций, а это n+1 запрос, где n-кол-во курсов.

Eager fetching позволяет уменьшить количество запросов в данному случае до 2-х: первый — это курсы, второй — все лекции к этим курсам. (Именно 2 запроса, в отличие от Doctrine, где в подобной ситуации будет выполнен 1 запрос). Для этого воспользуемся параметром **attach** для метода lmbActiveRecord :: find():

    $courses = lmbActiveRecord :: find('Course', array('attach' => 'lectures'));
    foreach($courses as $course)
    {
      echo 'Course ' . $course->getTitle() . ": \n";
      foreach($course->getLectures() as $lecture)
        echo '  Lecture ' . $lecture->getTitle() . ".\n";
    }

Начиная с релиза 2008.1 (или SVN-версия) lmbActiveRecord :: find() реализован таким образом, что все запросы от делегирует классу lmbARQuery, который является наследником от [lmbSelectRawQuery](../../../../dbal/docs/ru/dbal/intro_to_query_and_criteria.md). Наши примеры можно переписать след. образом:

    $lectures = lmbARQuery :: create('Lecture')->eagerJoin('course')->fetch();
    [...]

    $courses = lmbARQuery :: create('Course')->eagerAttach('lectures')->fetch();
    [...]

Подробнее об использовании lmbARQuery для eager будет рассказо ниже.

## Join-операции

**Join-операции** загружают связанные объекты при помощи **одного запроса, то есть расширяют базовый запрос** соответствующими полями связаных объектов и при получение данных формируют объекты-active_record из этих дополнительных данных.

Join-операции поддерживаются для следующих типов отношений:

* has_one
* belongs_to
* many_belongs_to

Также допускается использование вложенных join-ов. Поясним на примере. Пусть нам нужно вывести список всех лекций с указанием курса и программы этого курса. Одним запросом эти данные можно получить при помощи следующей операции:

    $lectures = lmbActiveRecord :: find('Lecture', array('join' => array('course' => array('join' => 'program'))));
    /* или, что тоже самое  */
    $lectures = lmbARQuery :: create('Lecture')->eagerJoin('course', array('join' => 'program'))->fetch();

Вот какой sql-запрос будет осуществлен в результате вышеуказанных операций:

    SELECT lecture.id AS id, lecture.title AS title, lecture.course_id AS course_id, 
           course.id AS course__id, course.title AS course__title, course.program_id AS course__program_id, 
           course__program.id AS course__program__id, course__program.title AS course__program__title
    FROM lecture 
    LEFT JOIN course AS course ON lecture.course_id = course.id 
    LEFT JOIN program AS course__program ON course.program_id = program__course.id

При получении данных поля с префиксом course__program__ будут переданы в объект класса Program, с префиксом course__ — в объект класса Course, остальные — в Lecture.

Поля префиксуются в соответствие с названиями отношений. Поля из таблицы program префиксуются 2 раза, это сделано чтобы не было конфликтов, например, если бы Lecture сама имел отношение с названием program.

Понимание того, как формируется конечный sql-запрос при join-операции иногда требуется, когда необходимо наложить какое-либо условие на данные связанных таблиц и чтобы в этом случае не было ошибок. Например, нам нужно вывести все лекции, которые относятся к программе с идентификатором 5:

    $lectures = lmbActiveRecord :: find('Lecture', array('join' => array('course' => array('join' => 'program')),
                                                         'criteria' => 'course_program.id = 5'));
    /* или, что тоже самое */
    $lectures = lmbARQuery :: create('Lecture')->eagerJoin('course', array('join' => 'program'))->where('course__program.id = 5')->fetch();

**Внимание** Eager fetching расставляет связи между загруженными объектами только в одну сторону. То есть лекции получают свои курсы при соответствующей join-операции, но курсы не получают при этом своих лекций!

## Attach-операции
**Attach-операции** загружают связанные объекты при помощи **дополнительно запроса** на каждое отношение.

Attach-операции поддерживаются для всех типов отношений, кроме composed_of. Обратите внимание, что attach можно использовать даже для тех отношений, где применяются join-операции, так как в некоторых случаях для этих отношений предпочтительно сделать все-таки дополнительный запрос к базе данных, чем выполнять запрос с LEFT JOIN.

Допускается использование вложенных attach-операций. Поясним это на примере. Допустим для нашей цепочки Program → Course → Lecture необходимо вывести список всех программ с курсами и указать все лекции для каждого курса. При помощи attach-операций, мы сможем загрузить все требуемые данные при помощи 3-х запросов:

    $programs = lmbActiveRecord :: find('Program', array('attach' => array('courses' => array('attach' => 'lectures'))));
    /* или, что тоже самое  */
    $lectures = lmbARQuery :: create('Program')->eagerAttach('courses', array('attach' => 'lectures'))->fetch();

В результате будут выполнены следующие 3 запроса к базе данных:

    SELECT program.id AS id, program.title AS title FROM program;
 
    SELECT course.id AS id, course.title AS title, course.program_id AS program_id
    FROM course  WHERE program_id IN (...) ORDER BY program_id ASC;
 
    SELECT lecture.id AS id, lecture.title AS title, lecture.course_id AS course_id
    FROM lecture WHERE course_id IN (...) ORDER BY course_id ASC;

При помощи дополнительных параметров к методам lmbARQuery :: eagerAttach() или к параметру attach мы можем задать, например, критерии выборки дополнительных объектов или их сортировку:

    $programs = lmbActiveRecord :: find('Program', array('attach' => array('courses' => array('criteria' => 'cost < 100'))));
    /* или, что тоже самое  */
    $lectures = lmbARQuery :: create('Program')->eagerAttach('courses', array('criteria' => 'cost < 100'))->fetch();

## Комбинирование Join и Attach-операции
Join- и Attach-операции можно легко комбинировать. Рассмотрим некоторые примеры запросов, которые можно сделать для нашей доменной модели:

    //
    $query->eagerJoin('course', array('join' => 'program'))->fetch();
    //
    $query->eagerAttach('courses', array('attach' => 'lectures'))->fetch();
    //
    $query = lmbARQuery :: create('Course', array(), $this->conn);
    $query->where(lmbSQLCriteria :: in('id', array($course_id1, $course_id2)));
    $rs = $query->eagerAttach('lectures', array('join' => 'alt_course'))->fetch();
    //
    $query = lmbARQuery :: create('Course');
    $query->where(lmbSQLCriteria :: in('id', array($course_id1, $course)id2)));
    $query->eagerAttach('lectures', array('join' => array('alt_course' => array('attach' => 'lectures'))))->fetch();
    //
    $query = lmbARQuery :: create('Lecture');
    $query->where(lmbSQLCriteria :: equal('course_id', $course_id));
    $query->eagerJoin('alt_course', array('attach' => 'lectures'))->fetch();
