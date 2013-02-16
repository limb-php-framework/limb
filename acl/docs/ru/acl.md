# Пакет ACL
Пакет ACL — класс, реализующий функционал [ACL](http://ru.wikipedia.org/wiki/ACL) и инфраструктура для его использования.
Пакет находится на стадии разработки, что может привести к небольшим изменениям в API.

## Доступ к объекту ACL
ACL представляет из себя «звездный» объект, и доступ к нему можно получить через toolkit.

    $acl_object = lmbToolkit::instance()->getAcl();
    lmbToolkit::instance()->setAcl($acl_object);

## Конфигурация ACL
В данный момент конфигурирование ACL возможно только через API. Удобнее всего оформить код конфигурации в виде фильтра для контроллера приложения.

### Создание ролей

    $acl->addRole('guest'); //регистрируем роль guest
    $acl->addRole('member', 'guest'); //регистрируем роль member, которая будет наследовать правила роли guest
 
    $acl->addRole('moderator');
    $acl->addRole('admin', array('moderator', 'member')); //множественное наследование ролей

### Создание ресурсов

    $acl->addResource('content'); //создание ресурса
    $acl->addResource('article', 'content'); //создание с наследованием
 
    $acl->addResource('front_content', 'content');
    $acl->addResource('front_article', array('front_content', 'article')); //множественное наследование

### Назначение правил
Назначение правила для привилегии

    $acl->addRole('guest');
    $acl->addResource('news');
    $acl->allow('guest', 'news', 'view');
 
    var_dump($acl->isAllowed('guest', 'news', 'view')); //true
    var_dump($acl->isAllowed('guest', 'news', 'edit')); //false
 
    $acl->allow('guest', 'news', array('view', 'comment');
    var_dump($acl->isAllowed('guest', 'news', 'view')); //true
    var_dump($acl->isAllowed('guest', 'news', 'comment')); //true

Назначение правила целиком на весь ресурс

    $acl->addRole('moderator');
    $acl->addResource('news');
    $acl->allow('moderator', 'news');
 
    var_dump($acl->isAllowed('moderator', 'news')); //true
    var_dump($acl->isAllowed('moderator', 'news', 'edit')); //true

Назначение правила на все ресурсы

    $acl->addRole('admin');
    $acl->allow('admin');

    $acl->addResource('news');
 
    var_dump($acl->isAllowed('admin', 'news')); //true
    var_dump($acl->isAllowed('admin', 'news', 'edit')); //true

## Использование ACL
Использование ACL по сути сводится к одному методу — lmbAcl::isAllowed(), но в зависимости от переданных данных процесс определения ролей и ресурсов идет по разному.

### Явное указание ролей и ресурсов

    $acl->isAllowed('marketing', 'content', 'view')); //имеет ли роль "marketing" привилегию "view", над ресурсом "content"

### Объекты, как носители роли или ресурса
Методу isAllowed в качестве роли можно передавать объект. Этот объект должен реализовывать интерфейс lmbRoleProviderInterface.

    class User implements lmbRoleProviderInterface
    {
      ...
      function getRole()
      {
        if($this->is_logged_in)
          return 'member';
        else
          return 'guest';
      }
    }
 
    class News implements lmbResourceProviderInterface
    {
      ...
      function getResource()
      {
        return 'news';
      }
    }
 
    $acl->addRole('guest');
    $acl->addRole('user', 'guest');
    $acl->addResource('news');
    $acl->allow('guest', 'news', 'comment');
    $acl->allow('user', 'news', 'vote');
 
    $user = new User;
    $news = new News;
 
    var_dump($acl->isAllowed($user, $news, 'comment'); //true
    var_dump($acl->isAllowed($user, $news, 'vote'); //false
 
    $user->setIsLoggedIn(true);
 
    var_dump($acl->isAllowed($user, $news, 'comment'); //true
    var_dump($acl->isAllowed($user, $news, 'vote'); //true

### Определение роли объекта в контексте объекта-ресурса
Очень часто объекты-роли, и объекты-ресурсы имеют внутренние связи друг с другом. Например пользователь может быть автором новости, или модератором конкретного раздела сайта. Для разрешения роли конкретного объекта-роли, в контексте определенного объекта-ресурса, объект-ресурс должен реализовывать интерфейс lmbRolesResolverInterface (метод getRoleFor()):

    class Article implements lmbRolesResolverInterface, lmbResourceProviderInterface
    {  
      function getRoleFor($object)
      {
        if($this->owner_id === $object->getId())
          return 'owner';
        if('Valtazar' === $object->name)
          return 'approver';
      }
 
      function getResource()
      {
        return 'article';
      }
    }
 
    class Member implements lmbRoleProviderInterface
    {
      public $name;  
 
      function __construct($name)
      {
        $this->name = $name;
      }
 
      function getRole()
      {
        return 'member';
      }
    }
 
    $member = new Member('Vasya Pupkin');
    $approver = new Member('Valtazar');
    $owner = new Member('Bob');
    $owner->save();
 
    $article = new Article();
    $article->setOwnerId($owner->getId());
 
    $this->acl->addRole('member');
    $this->acl->addRole('owner', 'member');
    $this->acl->addRole('approver', 'member');
    $this->acl->addResource('article');
 
    $this->acl->allow('member', 'article', 'view');
    $this->acl->allow('owner', 'article', 'edit');
    $this->acl->allow('approver', 'article', 'approve');
 
    var_dump($this->acl->isAllowed($member, $article, 'view')); //bool(true)
    var_dump($this->acl->isAllowed($member, $article, 'edit')); //bool(false)
    var_dump($this->acl->isAllowed($member, $article, 'approve')); //bool(false)
 
    var_dump($this->acl->isAllowed($owner, $article, 'view')); //bool(true)
    var_dump($this->acl->isAllowed($owner, $article, 'edit')); //bool(true)
    var_dump($this->acl->isAllowed($owner, $article, 'approve')); //bool(false)
 
    var_dump($this->acl->isAllowed($approver, $article, 'view')); //bool(true)
    var_dump($this->acl->isAllowed($approver, $article, 'edit')); //bool(false)
    var_dump($this->acl->isAllowed($approver, $article, 'approve')); //bool(true)

## MACRO тег
[{{allowed}}](../../../macro/docs/ru/macro/tags/acl_tags/allowed_tag.md)
