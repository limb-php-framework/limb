# lmbCachingFileLocator
lmbCachingFileLocator — кеширующий [lmbFileLocator](./lmb_file_locator.md), наследуется от lmbFileLocatorDecorator.

Использование lmbCachingFileLocator позволяет значительно сократить время на поиск файлов, так как поиск производится только один раз и далее результат запоминается в файле или памяти.

При кешировании учитывается содержимое параметра $params метода locate($alias, $params = array()) класса [lmb_file_locator](./lmb_file_locator.md).

Как правило lmbCachingFileLocator хранит закешированные локации в папке LIMB_VAR_DIR/locations - эта логика находится в методе lmbFsTools :: getFileLocator().
