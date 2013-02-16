# How {{macro}} searches template files by aliases?
macro can work both with *absolute* template file paths and *aliases* ( *relative* ones):

* /var/dev/project/template/news.phtml — absolute path
* news.phtml — an alias

macro resolves aliases using **template locator**. Template locator can be passed as a third argument of lmbMacroTemplate class, a default locator is used if one is not specified.

## In case of using {{macro}} directly
By default macro is looking for template files only in **templates/** folder of your application. You can change this by passing a fourth argument into constructor of lmbMacroConfig class.

    $config = new lmbMacroConfig($cache_dir = dirname(__FILE__ ) . '/cache/',
                                 $is_force_compile = false,
                                 $is_force_scan = false,
                                 $tpl_scan_dirs = array($dir1, $dir2, ...));
     
    $macro = new lmbMacroTemplate('tpl.phtml', $config);

## In case of using {{macro}} via VIEW package
VIEW creates macro template with an extended version of template locator that, apart from standard behaviour, looks for templates in **templates** folders of other Limb3 packages. The extended locator also caches resolved template paths in the file system. You can find locator cache files in var/locators/macro_locator.php file of your application.

You can specify directories list for template locator by redefining **LIMB_TEMPLATES_INCLUDE_PATH** constant, e.g.:

    define('LIMB_TEMPLATES_INCLUDE_PATH', 'my_path/design;/my/other/path/design_repository;template;limb/*/template');

directories are separated with **;** symbol, and * means any matches.

## How to implement your own template file aliases resolving strategy?
macro has a simple interface called lmbMacroTemplateLocatorInterface:

    interface lmbMacroTemplateLocatorInterface {
      function __construct($config);
      function locateSourceTemplate($file_name);
      function locateCompiledTemplate($file_name);
    }

You should just pass an object that implements this interfaces as a third argument of lmbMacroTemplate constructor.

    $macro = new lmbMacroTemplate('tpl.phtml', $config, new MyOwnTemplateLocator());
