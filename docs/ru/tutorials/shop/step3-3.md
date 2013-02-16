# Шаг 3.3 Редактирование и отображение профайла пользователя
## Редактирование своего профайла
Пользователь должен иметь возможность сменить свой пароль, а также поменять свои контактные данные (например, email) при необходимости. Для этого мы реализуем действие edit, которое будет в себя включать весь нужный функционал.

Начнем, пожалуй, с шаблона:

### Шаблон user/edit.phtml
Форма регистрации и форма редактирования профиля содержит много одинаковых полей. Воспользуемся этим обстоятельством и выделим общие поля в отдельный шаблон shop/template/user/include/form_fields.phtml:

Файл shop/template/user/include/form_fields.html:

    {{include file='_admin/form_errors.phtml'/}}
 
    <dl class="required field">
      <dt><label for="title">Login:</label></dt>
      <dd>{{input name='login' type='text' title='Login'/}}</dd>
    </dl>
 
    <dl class="required field">
      <dt><label for="name">Name:</label></dt>
      <dd>{{input name='name' type='text' title='Name'/}}</dd>
    </dl>
 
    <dl class="required field">
      <dt><label for="email">Email:</label></dt>
      <dd>{{input name='email' type='text' title='Email' error_class='error'/}}</dd>
    </dl>
 
    <dl class="field">
      <dt><label for="address">Delivery address:</label></dt>
      <dd>
        {{textarea name="address" id='address' type="text" title="Delivery address"/}}
      </dd>
    </dl>

Файл shop/template/user/edit.html:

    <? $this->title = 'Your profile'; ?>
    {{wrap with="front_page_layout.phtml" in="content_zone"}}
 
    This is your profile page. You can change some of your profile fields as well as your password<br/>
 
    <h2>Profile</h2>
    {{form id='profile_form' name='profile_form' method='post'}}
 
      {{include file='user/include/form_fields.phtml'/}}
      <hr/>
      <input id='edit' name='edit' type='submit' value='Update profile'/>
 
    {{/form}}
 
    <h2>Password change form</h2>
 
    {{form id='change_password_form' name='change_password_form' method='post'}}
 
      {{include file='_admin/form_errors.phtml'/}}
 
      <dl class="required field">
        <dt><label for="name">Old password:</label></dt>
        <dd>{{input name='old_password' type='text' title='Old password'/}}</dd>
      </dl>
 
      <dl class="required field">
        <dt><label for="name">Password:</label></dt>
        <dd>{{input name='password' type='text' title='Password'/}}</dd>
      </dl>
 
      <dl class="required field">
        <dt><label for="name">Repeat password:</label></dt>
        <dd>{{input name='repeat_password' type='text' title='Repeat password'/}}</dd>
      </dl>
 
      <hr/>
      <input id='change_password' name='change_password' type='submit' value='Change password'/>
    {{/form}}
    {{/wrap}}

Шаблон содержит 2 формы:

* одна — для изменения своих данных, таких как имя и email, а также адрес доставки.
* вторая — для смены своего текущего пароля.

Теперь мы покажем, как можно обрабатывать 2 формы при помощи одного действия в контроллере.

### Изменения в UserController

    <?php
 
    class UserController extends lmbController
    {
    [...]
    function doEdit()
      {
        $this->setFormDatasource($this->toolkit->getUser(), 'profile_form');
 
        if($this->request->has('change_password'))
          $this->_changeUserPassword();
        if($this->request->has('edit'))
          $this->_updateUserProfile();
      }
 
      protected function _changeUserPassword()
      {
        $this->useForm('change_password_form');
 
        $this->_validateChangePasswordForm();
 
        if($this->error_list->isValid())
        {
          $user = $this->toolkit->getUser();
          $user->setPassword($this->request->get('password'));
          $user->save();
 
          $this->flashMessage('Your password was changed');
          $this->toolkit->redirect();
        }
      }
 
      protected function _updateUserProfile()
      {
        $this->useForm('profile_form');
        $this->setFormDatasource($this->toolkit->getUser());
 
        $user_properties = $this->request->getPost(
          array('login', 'name', 'email', 'password', 'address')
        );
        $user = $this->toolkit->getUser();
        $user->import($user_properties);
 
        if($user->trySave($this->error_list))
        {
          $this->flashMessage('Your profile was changed');
          $this->toolkit->redirect();
        }
      }
 
      protected function _validateChangePasswordForm()
      {
        $this->validator->addRequiredRule('old_password');
        $this->_validatePasswordField();
 
        $user = $this->toolkit->getUser();
        if($old_password = $this->request->get('old_password'))
        {
          $hashed_password = User :: cryptPassword($old_password);
          if($user->getHashedPassword() != $hashed_password)
            $this->error_list->addError('Wrong old password', array('old_password'));
        }
      }
    [...]
    }
    ?>

Выбор той или иной функции по обработке формы по смене пароля или же по обновлению данных пользователя осуществляется очень просто - исходя из того, какая переменная пришла в request-е.

Мы также сразу передаем текущего пользователя в форму в шаблоне, так как желаем, чтобы эти данные всегда отображались в полях формы все зависимости от того, какая форма была до этого отправлена. Примем мы использовали только метод lmbController :: setFormDatasource($datasource, $form_id):

    $this->setFormDatasource($this->toolkit->getUser(), 'profile_form');

Это позволит не выводить ошибки валидации сразу рядом с двумя формами, если пользователь неправильно ввел информацию в форму по смене пароля.

Вот как должна выглядеть страница /user/edit:

![Alt-profile](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:shop:profile.png)

## Различные способы передачи данных в MACRO-шаблон
Для отображения информации по текущему пользователю необходимо передать объект пользователя в шаблон. Это можно сделать несколькими способами:

* Передавать пользователя в шаблон из контроллера. Для этого можно создать базовый контроллер, который будет заниматься передачей данных в шаблон, а остальные контроллеры отнаследовать от него.
* Получать пользователя при помощи php-вставки непосредственно в MACRO-шаблоне.
* Передавать данные о пользователе при помощи специального фильтра.
* Получение данных прямо в шаблоне через toolkit.

Мы покажем все способы и остановимся на получение данных в шаблоне через toolkit.

### Передача данных в шаблон из контроллера
Для этого способа необходоимо создать базовый класс BaseShopController со следующим кодом:

    class BaseShopController extends lmbController
    {
      function performAction()
      {
        $this->view->set('user', $this->toolkit->getUser());
        return parent :: performAction();
      }
    }

После этого в MACRO шаблоне объект пользователя будет доступен в корневом контейнере данных, то есть можно будет использовать такие выражения:

    <? if($this->user()->is_logged_in) { ?>
      Logged in as :{{$#user.name}}
    <? } ?>

### Использование php-вставок в MACRO-шаблоне
MACRO-позволяет использовать обычные php-вставки. Поэтому мы можем легко получить тулкит прямо в шаблоне и передать объект user в нужный нам контейнер данных, например:

    [...]
    <dl id="profile">
    <dt>Profile</dt>
    <dd>
    <? if(lmbToolkit :: instance()->getUser()->is_logged_in) { ?>
      Logged in as :{{$#user.name}}
    <? } ?>
    </dd>
    </dl>
    [...]

Если ваш верстальщик нормально относится к подобным php-вставкам в шаблонах — можете смело воспользоваться таким методом.

### Использование фильтра
Этот способ предусматривает добавление нескольких строк в фильтр:

    class SampleFilter implements lmbInterceptingFilter
    {
      function run($filter_chain)
      {
        $toolkit = lmbToolkit :: instance();
        $user = $toolkit->getUser();
        [...]
        $toolkit->getView()->set('user', $user);  
        [...]
      }
    }

Данная небольшая модификация позволит использовать user из корневого контейнера данных, как в способе с базовым контроллером.

### Получение данных прямо в шаблоне через toolkit
Начиная с версии пакета WEB_APP 0.4 (соответствует релизу 2007.2) toolkit поддерживает получение данных через базовые методы get(), к кому же toolkit стал доступен в шаблоне в корневой контейнере данных. Все это позволяет даже не заботиться о передаче user-а в шаблон, а действовать в стиле PullView:

файл shop/template/user/include/profile_box.phtml:

    <? if($user->is_logged_in) { ?>
    <dd>
      User: {$user.name}<br/>
      Login: {$user.login}<br/>
      Email: {$user.email}<br/>
      <a href="/user/edit/">edit</a>
      <a href="/user/logout/">logout</a>
    </dd>
    <? } else {?>
    <dd>
      <form method="POST" id='login_form' action='/user/login/'>
 
        <label for='login'>Login:</label><br/>
        <input type="text" name="login" id="login" title="Login" class='input'/><br/>
 
        <label for='passwd'>Password:</label><br/>
        <input type="text" name="password" id="password" type="password" title="Password" class='input'/><br/>
 
        <p>Use <b>admin</b>/<b>secret</b> to enter in <a href="/admin/">administration panel</a>.</p>
 
        <input type='submit' name='submitted' value="Submit"/><br/>
      </form>
      <a href="/user/register/">Register</a>
    </dd>
    <? } ?>

Файл shop/template/front_page_layout.phtml:

    [...]
        <div id="sidebar">
          <div id="navigation">
            <ul>
              <li><a href="product">Products</a></li>
              <li><a href="cart">Your Cart</a></li>
              <? if($this->toolkit->getUser()->is_logged_in) { ?>
                <li><a href="/user/orders/">Your Orders</a></li>
              <? } ?>
            </ul>
          </div>
 
          <dl id="profile">
            <dt>Profile</dt>
            {{include file="user/include/profile_box.phtml" user="$#toolkit.user"/}}
          </dl>
 
        </div>
    [...]

В этом примере, как вы видите, мы передаем переменную user в шаблон profile_box.

## Предварительные итоги
Если вы модифицировали шаблон front_page_layout.phtml, то теперь после аутентификации на странице /login вы сможете увидеть данные своей учетной записи в левой части страницы.

![Alt-login_success](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:shop:login_success.png)

## Далее
Для удобства работы с пользователями добваим CRUD в панель управления — [Шаг 3.4 Управление списком пользователей](./step3-4.md)
