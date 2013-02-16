# lmbIncludePathFileLocations
Класс lmbIncludePathFileLocations — класс-список локаций, который он формирует на основе набора списка суффиксов путей и элементов include_path.

Например:

    set_include_path( '/var/dev/my_project/' . PATH_SEPARATOR . '/var/dev/limb/');
 
    $locations = new lmbIncludePathFileLocations(array('tests/settings', 'settings'));

Полученный $locations вернет из метода getFileLocations() следующий список локаций:

* /var/dev/limb/tests/settings
* /var/dev/my_project/test/settings
* /var/dev/limb/settings
* /var/dev/my_project/settings

Этот список класс lmbIncludePathFileLocations формирует при помощи глобальной функции lmb_glob(). см. [список глобальных функций Limb3](../../../../core/docs/ru/core/global_functions.md)

Пример использования класса lmbIncludePathFileLocations можно увидеть в методе lmbFsTools :: getFileLocator(). Набор суффиксов путей для объектов класса lmbIncludePathFileLocations в Limb3 обычно задается при помощи какой-либо константы, которая используется (передается в lmbFsTools :: getFileByAlias()) в том или ином пакете более высокого уровня. см. [список констант, используемых в Limb3](../../../..//docs/ru/constants.md)
