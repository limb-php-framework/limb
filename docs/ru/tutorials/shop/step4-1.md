# Шаг 4.1 Управление списком пользователей
## Создание класса User
Т.к. мы уже умеем создавать модели, контроллеры и шаблоны, то позволим себе немного расслабится и воспользоваться возможностью [генерации сущностей](../../../../constructor/docs/ru/constructor.md):

    $ php ./lib/limb/limb.php entity_create
    Project directory [/www/limb-example-shop]: 
    Limb directory [/www/limb-example-shop/lib/]: 
    Table name (could be 'all'): user

После выполнения этих действий у нас должны были появится модель, контроллер и шаблоны, построенные по таблице **user**.

### Код класса User
Теперь можно дописать код класса User (shop/src/model/User.class.php):

    [...]
    protected $_default_sort_params = array('name' => 'ASC');
 
    protected $password;
 
    [...]
    protected function _createValidator()
    {
      $validator = new lmbValidator();
      $validator->addRequiredRule('login');
 
      $validator->addRequiredRule('email');
      $validator->addRequiredRule('name');
 
      lmb_require('src/validation/UserUniqueFieldRule.class.php');
      $validator->addRule(new UserUniqueFieldRule('login', $this));
      $validator->addRule(new UserUniqueFieldRule('email', $this));
 
      lmb_require('limb/validation/src/rule/lmbEmailRule.class.php');
      $validator->addRule(new lmbEmailRule('email'));
      return $validator;
    }
 
    protected function _onBeforeSave()
    {
      $this->_generatePassword();
    }
 
    protected function _generatePassword()
    {
      if($this->password)
        $this->setHashedPassword(self :: cryptPassword($this->password));
    }
 
    static function cryptPassword($password)
    {
      return md5($password);
    }
 
    [...]

Создаваемый в методе _createValidator() валидатор проверяет, что поля email и login должны быть заполнены и иметь уникальные значения. Последнее обеспечивается за счет использования правила UserUniqueFieldRule. Правило lmbEmailRule удостоверяется, что введенное в поле значение является электронным адресом.

Обратите внимание на метод **_onBeforeSave()**. Это так называемый метод расширения класса lmbActiveRecord, который дочерние классы могут использовать чтобы выполнять необходимые действия, например, перед сохранением новой записи, перед обновлением существующей, после сохранения и т.д. Список методов расширения класса lmbActiveRecord есть в разделе [«Расширение поведения базового класса lmbActiveRecord»](../../../../active_record/docs/ru/active_record/enhancement.md).

В нашем случае мы использовали этот метод, чтобы шифровать поле **password** и формировать значение поля **hashed_password**. Таким образом мы будем хранить только хешированный пароль, а обновление хешированного пароля будет происходить только, если в поле password есть новое значение.

## Доработка шаблонов и контроллера для управления пользователями
### Добавление ссылки в навигацию
Добавим ссылку в панель навигации.

Файл shop/settings/navigation.conf.php^

    [...]
    $conf[lmbCmsUserRoles :: ADMIN][0]['children'][] =
      array(
        "title" => "Пользователи",
        "url" => "/admin_user/",
        "icon" => "/shared/cms/images/icons/user.png",
    );

### Шаблоны
Шаблоны для действий create, edit и display просты и не составят большого труда. Особенно, если учесть, что они уже сгенерированы.

Остановимся только на шаблоне с полями формы:

Файл shop/template/admin_user/form_fields.phtml:

    <div class='tabs'>
 
      <ul>
        <li id='properties'><a>Основные свойства</a></li>
        <li id='address'><a>Адрес</a></li>
      </ul>
 
      <div id="tab_properties">
 
        <table class='fieldset'>
 
            <tr class="odd">
              <td class="field_name">{{label for="name"}}Имя:{{/label}}</td>
              <td class="field_value">{{input id="name" name="name" type="text" title="name"/}}</td>
              <td class="field_comment"></td>
            </tr>
 
            <tr class="even">
              <td class="field_name">{{label for="login"}}Логин:{{/label}}</td>
              <td class="field_value">{{input id="login" name="login" type="text" title="login"/}}</td>
              <td class="field_comment"></td>
            </tr>
 
            <tr class="odd">
              <td class="field_name">{{label for="password"}}Пароль:{{/label}}</td>
              <td class="field_value">{{input id="password" name="password" type="text" title="hashed_password"/}}</td>
              <td class="field_comment"></td>
            </tr>
 
            <tr class="even">
              <td class="field_name">{{label for="email"}}email:{{/label}}</td>
              <td class="field_value">{{input id="email" name="email" type="text" title="email"/}}</td>
              <td class="field_comment"></td>
            </tr>
        </table>
      </div>
 
      <div id="tab_address">
        <table class='fieldset'>
          <tr class="odd">
            <td class="field_name">{{label for="address"}}Адрес:{{/label}}</td>
            <td class="field_value">{{wysiwyg id="address" name="address" width="100%" height="300px" title="address"/}}</td>
            <td class="field_comment"></td>
          </tr>
        </table>
      </div>
      <div
 
    </div>

Тут есть два момента:

* мы использовались автогенерацией вкладок
* вместо hashed_password у нас есть поле password, которое, как мы помним хэшируется перед сохранением объекта

## Валидация пользователя
Реализация пользователей должна отвечать следующим требованиям:

* В системе не должно быть пользователей с одинаковыми логинами и email-адресами.
* Пароли пользователей не должны храниться в базе данных в открытом виде.

При помощи класса **UserUniqueFieldRule** мы будем проверять наличие одного уникального пользователя со значением какого-либо поля. Это правило валидации потом будет использоваться в классе **User**.

Файл shop/src/validation/UserUniqueFieldRule.class.php:

    <?php
    lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');
    lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
 
    class UserUniqueFieldRule extends lmbSingleFieldRule
    {
      protected $current_user;
 
      function __construct($field, $current_user)
      {
        $this->current_user = $current_user;
        parent :: __construct($field);
      }
 
      function check($value)
      {
        $criteria = new lmbSQLFieldCriteria($this->field_name, $value);
        if(!$this->current_user->isNew())
          $criteria->addAnd(lmbSQLCriteria::notEqual('id', $this->current_user->getId()));
 
        if(User :: findOne($criteria))
          $this->error('Пользователь со значением поля {Field} уже существует');
      }
    }

Класс UserUniqueFieldRule получает в конструкторе название поля, которое должно быть уникальным, и ссылку на текущего пользователя. Текущий пользователь понадобится нам при редактировании.

Класс **lmbSingleFieldRule** является базовым для правил валидации одного поля. Файл класса можно найти по пути limb/validation/src/rule/lmbSingleFieldRule.class.php Дочерние классы должны перекрывать метод **check($value)**, где $value — значение поля, которое необходимо проверить. Следует отметить, что check($value) не вызывается, если $value не содержит значимого значения. Поэтому совместно с этим правилом (его дочерними) рекомендуется использовать также правило **lmbRequiredFieldRule**. Об этом ты также упомянем чуть ниже при описании кода класса User.

При помощи [find-метода класса lmbActiveRecord](../../../../active_record/docs/ru/active_record/find.md) делается запрос к базе данных. В качестве второго параметра вместо $params можно передавать объект критерии, которая будет накладываться на выборку. Критерия — это объектная форма условия. В нашем случае использовался класс lmbSQLFieldCriteria, который принимает в конструкторе название поля и значение (по-умолчанию для этой пары формируется условие равенства). Использование критериев позволяет не думать о экранировании данных в SQL-запросах. Класс **lmbSQLCriteria** является фабрикой для критерий. Вы можете подробнее ознакомиться с классами [Criteria](../../../../dbal/docs/ru/dbal/criteria.md), однако это необязательно для понимания данного примера.

Метод lmbSingleFieldRule :: **error($error_string, $values = array())** добавляет ошибку в список ошибок валидации. Выражения вида {Field} будут заменены в процессе работы на реальные имена полей формы, с которой производится создание/редактирование пользователя.

## Далее
Мы рекомендуем вам, при помощи этой новой функциональности, добавить себе пользователя и приступить к следующему шагу: [«Шаг 4.2 Аутентификация пользователей»](./step4-2.md).
