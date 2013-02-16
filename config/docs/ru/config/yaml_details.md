# Конфигурационные файлы Yaml
LIMB3 позволяет хранить конфигурацию в формате Yaml.

Приведем пример конфигурацию роутов в формате yaml.

    MainPage:
      path: /
      defaults: 
        controller: main_page
        action: display
    #Yaml поддерживает стандартные комментарии: от символа # до конца строки    
    ControllerActionId:
      path: "/:controller/:action/:id"
      defaults: {action: display}
    ControllerAction: 
      path: "/:controller/:action"
      defaults:
        action: display
    Controller: {path: "/:controller"}

Конфигурацию в таком формате удобно записывать и читать. Дополнительным бонусом является поддержка PHP-кода внутри Yaml-файлов.

    ControllerAction: 
      path: "/:controller/:action"
      defaults:
        action: <?php echo lmbToolkit::getConf('action.conf.php')->get('default_action_name'); ?> #тут должен быть пробел.
    Controller: {path: "/:controller"}

Стоит обратить внимание на то, что если значение заканчивается закрывающим php-тегом, после него обязательно должен стоять пробел.

Код, работающий с Yaml, заимствован из компонента Yaml фреймворка Symfony: [Symfony YAML](http://symfony.com/components)
