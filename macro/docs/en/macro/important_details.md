# {{macro}} compilation and rendering. How to run {{macro}} template.
## Compile time vs run time
macro is a PHP compiling template engine, which basically means that macro creates PHP code while parsing the template markup. Generated PHP code is cached onto the disk, then it is included and executed. Since generated PHP code is cached, this provides macro a significant speed boost on later invocations for the same template file. Another nice speed bonus is the fact that generated PHP code can also be cached by some decent PHP accelerator(e.g eAccelerator) which makes template execution really blazing fast.

In other words, macro processes templates in two stages:

* a compiling stage ( **compile time** )
* an execution stage ( **run time** )

Since compilation is rather complex and CPU intensive job for the template engine you can switch off template re-compilation(thus, enabling caching) by changing settings in /settings/**macro.conf.php** file (if you use macro with WEB_APP package) or by passing proper **lmbMacroConfig** params (see an example below). Template re-compilation is usually «off» on the production server and «on» on devbox.

Compiled templates can be found in **var/compiled** folder of your application(however, this can be changed).

## How {{macro}} compiles templates
macro parses template at compile time and creates so called compile time **tree** (a sort of DOM). The nodes of this tree are objects of lmbMacroNode class. Tags, output expressions and regular text blocks become nodes of this tree.

macro gives you a lot of power for templates composition:

* **including** — inserts the content of other template into current template
* **wrapping** — wraps part or the whole content of the current template and inserts it into some particular point (slot) or points of other template.
* **reusing** — uses the same piece of template several times in different places

All these features greatly affect the compilation process. macro **compiles any template (whatever composite and nested) into one file**. This is very important to understand. In other words, if you have a template that wraps or includes other template(-s) then macro will compose a joint template first and compile it after that. All these features make it hard for macro to support automatic re-compilation mode (on template change) like in Smarty and you have to explicitly enable re-compilation during template development.

[See more about "Templates composition"](./template_composition.md).

Once compile time tree is created, macro compiler walks through the tree and asks every node to generate a portion of the compiled template. The result is wrapped into PHP class with unique name and saved into file in /var/compiled folder

## How to run {{macro}} template?
First you need to get macro package itself. The easiest way to get macro is to use PEAR channel:

    pear channel-discover pear.limb-project.com
    pear install limb/macro-alpha

You can also consider checking out Limb3 from SVN or downloading the bundled release from SourceForge. [See Getting Limb3](../../../../docs/ru/how_to_download.md).

Next let's create a folder somewhere on your hard drive, say ~/macro_test/

Here is a sample macro template:

    <body>
    <h1>Newsline</h1>

    {{list using="$#news"}}
    <ul>
      {{list:item}}
        <li>[{$item.date}] <b>{$item.title}</b> </li>
      {{/list:item}}
    </ul>
    {{/list}}
    </body>

Create a file news.phtml with template code shown above and put it into **~/macro_test/templates/** folder.

Here is the code to parse and execute our template:

    <?php
    set_include_path(dirname(__FILE__) . '/' . PATH_SEPARATOR .
                    '/path/to/limb/' . PATH_SEPARATOR . //if you are using PEAR installation, this line can be omitted
                    get_include_path());

    require_once('limb/macro/common.inc.php');

    $config = new lmbMacroConfig($cache_dir = dirname(__FILE__ ) . '/cache/', //setting up directory for compiled templates
                                 $is_force_compile = false,
                                 $is_force_scan = false);

    $macro = new lmbMacroTemplate('news.phtml', $config);

    $test_news = array(array('date' => '2007-01-12', 'title' => 'test news1'),
                              array('date' => '2007-01-13', 'title' => 'test news2'));

    $macro->set('news', $test_news);
    echo $macro->render();

Save this code as macro_example.php file.

You also need to create **~/macro_test/cache/** folder with write permissions.

Now you can run the script with «php ~/macro_test/macro_example.php» command. You should get:

    <body>
    <h1>Newsline</h1>
    <ul>
        <li>[2007-01-12] <b>test news1</b> </li>
        <li>[2007-01-13] <b>test news2</b> </li>
    </ul>
    </body>

Now go to ~/macro_test/cache. There should be 3 files: tags.cache, filters.cache, and a PHP file with long and weird looking name. The last one - is a compiled news.phtml template.

Let's take a look at the compiled news.phtml (it's re-formatted PHP a bit here since it's not so readable actually):

    <?php
    if(!class_exists('MacroTemplateExecutor47662db85cf6e', false))
    {
    require_once('limb/macro/src/compiler/lmbMacroTemplateExecutor.class.php');
    class MacroTemplateExecutor47662db85cf6e extends lmbMacroTemplateExecutor
    {
      function render($args = array())
      {
        if($args) extract($args);
        $this->_init();
         ?>
        <body>
        <h1>Newsline</h1>

        <?php $C = 0;$D = $this->news;
        foreach($D as $item) {
          if($C == 0) { ?>
          <ul>
          <?php } ?>

          <li>[<?php $F='';
          $G = $item;
          if((is_array($G) || ($G instanceof ArrayAccess)) && isset($G['date'])) {
            $F = $G['date'];
          }else{
            $F = '';
          }
          echo $F;
           ?>] <b><?php $H='';
          $I = $item;
          if((is_array($I) || ($I instanceof ArrayAccess)) && isset($I['title'])) {
            $H = $I['title'];
          }else{
            $H = '';
          }
          echo htmlspecialchars($H,3);
           ?></b> </li>
            <?php $C++;} ?>

          <?php if($C > 0) { ?>
          </ul>
        <?php } ?>

        </body>
        <?php
        }
      }
    }
    $macro_executor_class='MacroTemplateExecutor47662db85cf6e';

## lmbMacroTemplate class
lmbMacroTemplate methods :

* **set($var, $value)** — assigns $value to the global variable with $var name. This variable will be available in template via **{$this→var}** or **{$#var}** expression($# is an alias for $this→).
* **setVars($vars)** — assigns several global variables in a row, just like you called set(..) several times. Removes previously assigned variables.
* **render()** — executes compiled template and returns result as a string.

lmbMacroTemplate constructor accepts the following parameters:

* **$template_path** — relative or absolute path to the template file
* **$config** — configuration settings, an object of lmbMacroCofig class or array with similar fields.
* **$locator** — templates locator which defines how relative template paths should be resolved, an object of lmbMacroTemplateLocator class or any object that supports lmbMacroTemplateLocatorInterface interface.

If $config or $locator are not defined then lmbMacroTemplate will use default settings(which are fine in most cases).

## Further reading
* [{{macro}} compiler](./compiler.md) — almost everything you need to know to understand how macro actually compiles templates. This page is recommended for developers who want to extend macro by creating their own custom tags and filters.
