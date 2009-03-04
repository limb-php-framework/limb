<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');
require_once('limb/wact/tests/cases/WactTestTemplateLocator.class.php');

class WactTestTemplateLocatorCase extends WactTemplateTestCase {

  protected $locator;

  function setUp()
  {
    parent :: setUp();

    $this->locator = new WactTestTemplateLocator($this->default_config);
  }

  function testLocatorRegisterTemplateNoAlias()
  {
    $this->locator->registerTestingTemplate($template_file_path = 'template.html',
                                            $templateContent = 'TemplateCode');

    $this->assertEqual($this->locator->locateSourceTemplate($template_file_path),
                       $template_file_path);

    $this->assertEqual($this->locator->locateCompiledTemplate($template_file_path),
                       $this->default_config->getCacheDir().'/'. $template_file_path . '.php');

    $this->assertEqual($this->locator->readTemplateFile($template_file_path),
                       $templateContent);
  }

  function testLocatorRegisterTemplateWithAlias()
  {
    $this->locator->registerTestingTemplate($template_file_path = '/path/to/template.html',
                                            $templateContent = 'TemplateCode',
                                            $template_file_name = 'template.html');

    $this->assertEqual($this->locator->locateSourceTemplate($template_file_name),
                       $template_file_path);

    $this->assertEqual($this->locator->locateCompiledTemplate($template_file_name),
                       $this->default_config->getCacheDir().'/'. $template_file_path . '.php');

    $this->assertEqual($this->locator->readTemplateFile($template_file_path),
                       $templateContent);
  }
}

