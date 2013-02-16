# lmbConf
lmbConf — класс, позволяющий в качестве конфигурационной информации использовать php-файл, в котором определена переменная $conf. lmbConf является дочерним классом от класса [lmbSet](../../../../core/docs/ru/core/lmb_set.md).

Например, у нас есть файл my_conf.php следующего содержимого:

    <?php
 
    $conf = array(
    'AdminPanel' =>
      array('path' => '/admin',
            'defaults' => array('service' => 'AdminPanel',
                                'action' => 'admin_display')),
 
    'EdPrograms' =>
      array('path' => '/admin/programs/:action',
            'defaults' => array('service' => 'EdPrograms',
                                'action' => 'admin_display'))
    );
 
    ?>

Тогда его использование будет выглядеть следующим образом:

    $conf = new lmbConf('my_conf.php');
    $conf->get('AdminPanel'); // вернет array('path' => '/admin', 'defaults' => [...]))

Внутри php-файла, который будет подключен в lmbConf классе мы можем использовать любые прочие операторы, вызовы и т.д. - все, что необходимо для формирования конфигурационных данных. Это может быть полезно, если конфигурационные данные формируются в соотвествии с какой-либо логикой.

## Override файлы
Класс lmbConf также ищет так называемые override-файлы, которые позволяют перекрывать базовые свойства, определенные в оригинальных файлах. override-файл ищется там же, где и оригинальный файл. Имя override файла формируется след. образом: до расширения .php вставляется суффикс .override, например:

    $original_file = '/settings/db.conf.php';
    $override_file = '/settings/db.conf.override.php';
