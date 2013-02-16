# Функции для проверки данных
Все функции бросают исключение **lmbInvalidArgumentException**, если переданный параметр не удовлетворяет условию.

## lmb_assert_true( $value, [$message, [$exception_class]] )
Проверка на «положительность» параметра.

    lmb_assert_true(true);
    lmb_assert_true(false); //исключение
    lmb_assert_true(false, 'Ну что же вы мне все врете. Правды хочу!'); //исключение с пользовательским текстом сообщения
 
    lmb_assert_true(1);
    lmb_assert_true(0); //исключение
 
    lmb_assert_true(1.1);
    lmb_assert_true(0.0); //исключение
 
    lmb_assert_true('foo');
    lmb_assert_true(''); //исключение
 
    lmb_assert_true(array(1));
    lmb_assert_true(array()); //исключение
 
    lmb_assert_true(new stdClass()); // все верно, нет исключения

## lmb_assert_type ($value, $expected_type, [$message, [$exception_class]] )
Проверка параметра на принадлежность определенному типу.

    //Простые типы
    lmb_assert_type(true, 'bool');
    lmb_assert_type(false, 'boolean'); //alias для bool
    lmb_assert_type(0, 'boolean'); //исключение
    lmb_assert_type(0, 'boolean', 'Ну не булеан это!'); //исключение с пользовательским текстом сообщения
 
    lmb_assert_type(0, 'integer');
    lmb_assert_type(1, 'numeric'); //alias для integer
    lmb_assert_type(false, 'numeric') //исключение
 
    lmb_assert_type(0.0, 'float');
    lmb_assert_type(0xfffffffffffffffffffff, 'double') //alias для float
    lmb_assert_type(1, 'double') //исключение
 
    lmb_assert_type('1', 'string');
    lmb_assert_type(1, 'string') //исключение
 
    lmb_assert_type(array(), 'array');
    lmb_assert_type(new ArrayObject, 'array') //SPL-класс ArrayObject имплементирует интерфейс ArrayAccess
    lmb_assert_type(1, 'array') //исключение
 
    lmb_assert_type(new stdClass(), 'object');
    lmb_assert_type(1, 'object') //исключение
 
    //Классы (и, соответственно, интерфейсы) объектов
    lmb_assert_type(new ArrayObject, 'ArrayObject');
    lmb_assert_type(new ArrayObject, 'ArrayAccess');
    lmb_assert_type(new ArrayObject, 'SomeClass') //исключение

## lmb_assert_reg_exp ( $string, $pattern, [$message, [$exception_class]] )
Проверка строки на совпадение с регулярным выражением.

    //простой поиск подстроки
    lmb_assert_reg_exp(array(), 'a'); //исключение, т.к. не строка
 
    lmb_assert_reg_exp('abc', 'a');
    lmb_assert_reg_exp('abc', 'x'); //исключение
    lmb_assert_reg_exp('abc', 'x', 'В вашей строке "x" не найдено'); //исключение с пользовательским текстом сообщения
 
    //PCRE шаблоны
    lmb_assert_reg_exp('abc', '/b/'); 
    lmb_assert_reg_exp('abc', '/x/'); //исключение

## lmb_assert_array_with_key ( $array, $key, [$message, [$exception_class]] )
Проверка наличия ключа в массиве.

    lmb_assert_array_with_key(1, 'foo'); //исключение, т.к. не массив
 
    lmb_assert_array_with_key(array('foo' => 1), 'foo');
    lmb_assert_array_with_key(array('foo' => 1, 'bar' => 2), array('foo', 'bar')); //проверка сразу на несколько ключей
    lmb_assert_array_with_key(array('foo' => 1), 'bar'); //исключение
    lmb_assert_array_with_key(array('foo' => 1), 'bar', 'В вашем "фу" "бара" не найдено'); //исключение с пользовательским текстом сообщения
