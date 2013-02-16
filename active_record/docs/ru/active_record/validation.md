# Валидация данных в объектах
## Механизм валидации данных в Limb3
Мы рекомендуем вам познакомиться с разделом [«Валидация данных»](../../../../web_app/docs/ru/web_app/validation.md) для ознакомления с механизмом валидации в Limb3.

После прочтения этого раздела вам будет понятно, как осуществляется встроенная валидация в классе lmbActiveRecord.

Отметим сразу, что lmbActiveRecord применяет валидатор для проверки самого себя, то есть передает $this в метод lmbValidator :: validate($datasource).

## Валидация при сохранении
lmbActiveRecord содержит встроенные средства для проверки данных.

Валидация осуществляется автоматически при вызове метода lmbActiveRecord :: **save($error_list = null)**, который принимает в качестве необязательного параметра объект списка ошибок валидации **$error_list**.

lmbActiveRecord содержит 2 защищенных фабричных метода:

* lmbActiveRecord :: **_createInsertValidator()**
* lmbActiveRecord :: **_createUpdateValidator()**

Эти методы создают валидатор, который используется для проверки самого объекта перед сохранением. То, какой валидатор создается, определяется исходя из того, является ли объект новым или же уже существующим. Это касается, как валидации при сохранении, так и при непосредственном вызове метода validate().

По-умолчанию эти методы реализованы к классе lmbActiveRecord таким образом, что вызывают еще один метод - lmbActiveRecord :: **_createValidator()**. Обычно в дочернем классе перекрывается именно этот метод, например:

    class Lesson extends lmbActiveRecord
    {
      function _createValidator()
      {
        $validator = new lmbValidator();
        $validator->addRequiredRule('title');
        $validator->addRequiredObjectRule('topic', 'Topic');
        $validator->addRule(new ValidLessonPeriodRule('date_start', 'date_end', $this));
        return $validator;
      }
    }

Метод lmbValidator :: **addRequiredRule($field_name)** создан для удобства добавления правила для обязательных к заполнению полей.

Метод lmbActiveRecord :: save() генерирует исключение класса **lmbValidationException**, если валидация данных не прошла:

    try  {
      $active_record->save();
    }
    catch(lmbValidationException $e){
      echo "Объект содержит неверные данные";
    }

Если в save() объект списка ошибок не передан, lmbActiveRecord создаст свой собственный, который можно получить при помощи метода lmbActiveRecord :: **getErrorList()**.

    try  {
      $active_record->save();
    }
    catch(lmbValidationException $e){
      $error_list = $active_record->getErrorList();
      echo "Объект содержит неверные данные:\n";
      foreach($error_list as $error);
        echo $error->getErrorMessage()"\n";
    }

Также в процессе работы там показалось удобным иметь метод lmbActiveRecord :: **_onValidate()**, который дочерние классы могут перекрывать для вставки своих проверок, которые неудобно или нецелесообразно реализовывать в виде классов, например:

    protected function _onValidate()
    {
      if($this->isValid() && $this->getDateStart()->isAfter($this->getDateEnd()))
        $this->_addError('Дата начала не может быть позже даты окончания');
    }

Здесь мы возпользовались методами lmbActiveRecord :: **isValid()** и **_addError($message, $fields = array(), $values = array())**. Первый возвращает true, если $this→_error_list не содержит ни одной ошибки, а второй - добавлять ошибку $message в $this→_error_list, которая относится к полям $fields, которые содержали значения $values.

см. также [«Валидация данных в Limb3»](../../../../web_app/docs/ru/web_app/validation.md)

## Отдельная валидация данных
Иногда необходимо валидировать данные отдельно, без вызова метода save(). В этом случае можно использовать метод lmbActiveRecord :: **validate($error_list = null)**, который также принимает $error_list в качестве необязательного параметра.

При возникновении ошибок метод lmbActiveRecord :: validate() уже не генерирует исключение, как это делает метод save().

То, какой валидатор будет использован (insert или update, см. выше), также зависит от статуса, новый ли валидируется объект ActiveRecord или уже старый (загруженный).

При использовании отдельной валидации, смысла использовать save(), который еще раз будет проверять данные уже нет, поэтому lmbActiveRecord содержит метод **saveNoValidation()**, который используется в этом случае, например:

    $error_list = new lmbErrorList();
    $user->validate($error_list); 
    $document->validate($error_list);
    if(!$error_list->isEmpty())
      echo "Ошибки в процессе валидации данных!";
    else
    {
      $user->saveNoValidation();
      $document->saveNoValidation();
    }
