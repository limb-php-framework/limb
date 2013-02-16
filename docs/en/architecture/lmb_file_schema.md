# lmbFileSchema
## Description
lmbFileSchema is a mechanism used in Limb to find full file paths by short aliases. lmbFileSchema is used by different class factories, template engine, etc.

## UML Static structure
## Comments
Here is the sample code that uses lmbToolkit to get access to lmbFileSchema in order to locate full file path of a command:

    $file_schema = lmbToolkit :: instance()->getFileSchema();
    $command_full_path = $file_schema->locateCommand($alias = 'Set404ErrorCommand');
    require_once($command_full_path);

lmbFileSchema delegates file name resolving to different file locators. [lmbFileLocator](../../../fs/docs/en/fs/lmb_file_locator.md) implements the following interface:

    interface FileLocatorInterface
    {
      function locate($alias, $params);
      function getLocations();
    }

The delegation looks like this:

    class lmbFileSchema
    { 
      protected $command_locator; 
      [...]
      function locateCommand($alias)
      {
        return $this->command_locator->locate($alias);
      }
    }

File locators look for files in different locations(folders) which they receive from [lmbFileLocations](../../../fs/docs/en/fs/lmb_file_locations.md). lmbFileLocations interface consists of the only getLocations method returning an array of folders where file should be searched:

    interface lmbFileLocations
    {
      function getLocations();
    }

lmbFileSchema has factory methods to create FileLocators so you can extend lmbFileSchema and create concrete FileLocators you need:

    class MyLimbFileSchema extends lmbFileSchema
    { 
      [...]
      protected function _createCommandLocator()
      {
        $locations = new lmbAndPackagesFileLocations();
        return new lmbCachingFileLocator(new lmbFileLocator($locations, '/command/%s.class.php'));
      } 

In this example we create caching CommandLocator that will look for files in LPKG_CORE_DIR and in packages applying '/command/%s.class.php' pattern. For example if asked to locate «lmbBaseCommand» alias, it will return 'LPKG_CORE_DIR/src/command/lmbBaseCommand.class.php'

## Frequently used classes

Class name | Description
-----------|------------
[lmbFileLocationsList](../../../fs/docs/en/fs/lmb_file_locations_list.md) | Allows you to create compound lmbFileLocations.
[lmbPackagesFileLocations](../../../fs/docs/en/fs/lmb_packages_file_locations.md) | Returns paths to packages install for a Limb-based application. Uses [lmbPackagesInfo]((../../../fs/docs/en/fs/lmb_packages_info.md) to get information about installed packages.
[TemplateFileLocations](../../../fs/docs/en/fs/template_file_locations.md) | Returns paths where template files should be looked for. Takes into account current locale and common.ini file setting: templates_path and shared_templates_path.
