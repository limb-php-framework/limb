# Шаг 3.2 Аутентификация пользователей
## Хранение данных пользователя в сессии
### Что такое toolkit?
Небольшое отступление…

Некоторые объекты являются особо популярными, и зачастую требуется иметь глобальный доступ к таким объектам в рамках всей системы (приложения). К таким объектам можно отнести Запрос (Request), Ответ (Response), Пользователь (User), Подключения к базам данных (DbConnection), различные фабрики (Factory) и т.д.

Есть несколько решений:

* Использовать [паттерн Singleton (одиночка)](http://cpp-reference.ru/patterns/creational-patterns/singleton/) для реализации таких объектов.
* Складывать такие объекты в общее хранище и передавать это хранилище между всеми объектами приложения. Часто такое решение называют контекст **Context**.

В Limb выбран комбинированный вариант. Если просто, то мы создали только 1 одиночку, в которой хранятся все нужные в приложении объекты, и назвали ее Toolkit.

Итак, **Toolkit (тулкит)** — это такой объект, через который можно получить какой-либо часто используемый или важный объект в любом месте программы. Тулкит реализован через класс **lmbToolkit**

Для получения доступа к тулкиту используется метод **lmbToolkit :: instance()**.

Пример использования тулкита:

    $toolkit = lmbToolkit :: instance();
    $connection = $toolkit->getDefaultDbConnection();

Особенностью тулкита является то, что его интерфейс четко не определен, а зависит от так называемых инструментов — **tools**. Тулкит по сути состоит из наборов инструментов, вернее tools можно **регистрировать** в тулките. Именно tools определяют, что умеет тулкит (какие методы у него можно вызывать). Для регистрации нового набора инструментов в тулките существует метод lmbToolkit :: merge($tools).

Например, создадим свой tools и зарегистрируем его в lmbToolkit:

    class MyTools extends lmbAbstractTools
    {
      function getSomethingUseful()
      { 
        return [...];
      }
    }
 
    lmbToolkit :: merge(new MyTools()); // :: instance() для добавления уже вызывать не нужно
 
    [...]
    // Где-то в клиентском коде
    $toolkit = lmbToolkit :: instance();
    $my_object = $toolkit->getSomethingUseful();

Наборы инструментов можно заменять, то есть мы можем зарегистировать в тулките другой tools, который будет поддерживать интерфейс MyTools и для клиентского кода эта подмена будет совершенно прозрачной.

Начальное заполнение тулкита наборами инструментов производится в файле limb/web_app/toolkit.inc.php.

Этой информации пока нам вполне достаточно, чтобы понять наши дальнейшие действия. Более подробно о тулките в [описании пакета Toolkit](../../../../docs/toolkit/docs/ru/toolkit.md).

Для нашего примера мы создадим наш собственный набор инструментов ShopTools и сделаем так, чтобы он поддерживал метод getUser(), который будет возвращать пользователя из сессии.

### Работа с сессией в Limb
Limb не запрещает вам работать с сессиями через глобальную переменную $_SESSION, однако представляет другой, лучший с нашей точки зрения способ, со своими преимуществами.

Для работы с сессией, в Limb есть специальный объект session, который можно получить через тулкит:

    $session = lmbToolkit :: instance()->getSession();
    $session->set('my_var', $value); // Поставить значение в сессию
    $session->destroy('my_var');  // Удалить значение из сессии

Работа с сессией осуществляется при помощи пакета [SESSION](../../../../session/docs/ru/session.md).

Преимущества использования пакета SESSION:

* Возможность легкой смены способа хранения сессионных данных. На данный момент поддерживается хранение сессионных данных в базе данных и при помощи файлов.
* Автоматическое подключение необходимых классов до десериализации сессии. Вам не нужно больше об этом заботиться.
* Единый set/get интерфейс для установки и получения данных из сессии. Это позволяет в случае необходимости, например, передать сессию в WACT-шаблон в качестве контейнера с данными.
* Легкое установление и сброс фикстур при модульном тестировании.

### Хранение пользователя в тулките
Для того, чтобы хранить информацию о том, какой пользователь загеристрирован в приложении, нам необходимо хранить только его идентификатор. Этого будет вполне достаточно. А объект класса User мы будем хранить в ShopTools, которые мы зарегистрируем в тулките. Это позволит нам получить доступ к пользователю в любом месте программы.

Итак, класс **ShopTools**.

Файл shop/src/toolkit/ShopTools.class.php:

    <?php
    lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
    lmb_require('src/model/User.class.php');
 
    class ShopTools extends lmbAbstractTools
    {
      protected $user;
 
      function getUser()
      {
        if(is_object($this->user))
          return $this->user;
 
        $session = lmbToolkit :: instance()->getSession();
        if($user_id = $session->get('user_id'))
        {
          try
          {
            $this->user = new User($user_id);
            $this->user->setIsLoggedIn(true);
          }
          catch(lmbARException $e)
          {
            $this->user = new User();
            $session->remove('user_id'); 
          }
        }
        else
          $this->user = new User();
 
        return $this->user;
      }
    }

Класс ShopUser наследуется от **lmbAbstractTools**, который используется в качестве базового для большинства наборов инструментов. lmbAbstractTools добавляет в тулкит поддержку всех методов класса (свои и методы дочернего класса), что нам в данном случае и нужно.

Метод getUser() по сути реализует lazy loading для пользователя. Если идентификатор пользователя находится в сессии, значит пользователь был уже залогинен в приложении - и мы загружаем его запись из базы данных. Если же идентификатора пользователя в сессии нет — значит можно создать пустой объект класса User.

Обратите внимание на блок try, catch, который ловит исключение класса lmbARException. Если пользователь с указанным удентификаторов был удален, то при инициализации User с идентификаторов, будет сгенерировано исключение lmbARException. В этом случае мы также должны инициализировать пустой объект класса User и удалить значение user_id из сессии.

### Регистрация ShopTools в тулките
Наш набор инструментов ShopTools необходимо зарегистрировать в тулките. Добавим регистрацию в файл setup.inc.php.

Файл shop/setup.inc.php:

    [...]
    lmb_require('src/toolkit/ShopTools.class.php');
    lmbToolkit :: merge(new ShopTools());

Теперь у нас есть все чтобы начать реализовывать процедуру аутентификации пользователей, отображение профайла на страницах сайта и проверку прав доступа к панели управления.

## Аутентификация пользователей
### Изменения в классе User
Для начала внесем изменения в класс User, чтобы он хранил информацию о том, зарегистрирован ли пользователь или нет, а также функционал по аутентификации:

    class User extends lmbActiveRecord
    {
      [...]
      protected $is_logged_in = false;
      [...]
 
      function login($login, $password)
      {
        $hashed_password = User :: cryptPassword($password);
 
        $criteria = new lmbSQLFieldCriteria('login', $login);
        $criteria->addAnd(new lmbSQLFieldCriteria('hashed_password', $hashed_password));
 
        if($user = lmbActiveRecord :: findFirst('User', array('criteria' => $criteria)))
        {
          $this->import($user);
          $this->setIsNew(false);
          $this->setIsLoggedIn(true);
          return true;
        }
        else
        {
          $this->setIsLoggedIn(false);
          return false;
        }
      }
 
      function logout()
      {
        $this->removeAll();
        $this->is_logged_in = false;
      }
    }

### Контроллер UserController
За аутентификацию пользователей будет отвечать контроллер UserController :

Файл shop/src/controller/UserController.class.php:

    <?php
 
    class UserController extends lmbController
    {
      function doLogin()
      {
        if(!$this->request->hasPost())
          return;
 
        $user = $this->toolkit->getUser();
 
        $this->useForm('login_form');
        $this->setFormDatasource($this->request);
 
        $this->_validateLoginForm();
 
        if(!$this->error_list->isValid())
          return;
 
        $login = $this->request->get('login');
        $password = $this->request->get('password');
 
        if(!$user->login($login, $password))
        {
          $this->addError('Login or password incorrect!');
        }
        else
        {
          $this->toolkit->getSession()->set('user_id', $user->getId());
          $this->flashAndRedirect('You were logged in!', '/');
        }
      }
 
      protected function _validateLoginForm()
      {
        $this->validator->addRequiredRule('login');
        $this->validator->addRequiredRule('password');
        $this->validator->validate($this->request);
      }
 
      function doLogout()
      {
        $user = $this->toolkit->getUser();
        $user->logout();
        $this->toolkit->getSession()->remove('user_id');
        $this->response->redirect('/');
      }
    }

Большинство кода должны быть уже понятным, но некоторые моменты мы поясним.

Логика работы **UserController :: doLogin()** должна быть понятной: если процедура аутентификации прошла успешно, мы должны сохранить идентификатор пользователя в сессии. Аналогично, если пользователь выходит из приложения (doLogout()), мы должны удалить его идентификатор из сессии.

После аутентификации мы перебрасываем пользователя на стартовую страницу.

### Шаблон user/login.phtml
Файл shop/template/user/login.phtml:

    <? $this->title='Login'; ?>
    {{wrap with="front_page_layout.phtml" into="content_zone"}}
    {{form method="POST" id='login_form'}}
 
      {{include file='form_errors.phtml'/}}
 
      <div class="field">
      <label for='login'>Login:</label>
      {{input type="text" name="login" id="login" title="Login" class='input'/}}
      </div>
 
      <div class="field">
      <label for='passwd'>Password:</label>
      {{input type="text" name="password" id="password" type="password" title="Password" class='input'/}}
      </div>
 
      <input type='submit' class='button' name='submitted' value="Submit" class='button'/>
 
    {{/form}}
    {{/wrap}}

## Промежуточные результаты
Попробуем зайти на страницу /user/login нашего приложения и ввести любые данных в поля для ввода логина и пароля:

![Alt-login_error](http://wiki.limb-project.com/2011.1/lib/exe/fetch.php?cache=&media=limb3:ru:tutorials:shop:login_error.png)

Мы можете также попробовать ввести логин и пароль учетной записи, созданной вами ранее. В этом случае вы увидите надпись «You were logged in!».

## Далее
Теперь у нас есть функционал по аутентификации пользователей, но толку от него пока немного.

Итак, следующий шаг: [Шаг 3.3 Редактирование и отображение профайла пользователя](./step3-3.md).
