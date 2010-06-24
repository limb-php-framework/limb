<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/WactTemplate.class.php');
require_once('limb/wact/src/compiler/templatecompiler.inc.php');
require_once('limb/wact/tests/cases/WactTestTemplateConfig.class.php');
require_once('limb/wact/tests/cases/WactTestTemplateLocator.class.php');

class WactTemplateTestCase extends UnitTestCase
{
  protected $default_config;
  protected $default_locator;

  function setUp()
  {
    $this->default_config = new WactTestTemplateConfig(array('scan_directories' => array('limb/wact/src/tags/'),
                                                             'cache_dir' => WACT_CACHE_DIR,
                                                             'force_scan' => 1,
                                                             'force_compile' => 1,
                                                             'sax_filters' => array()));

    $this->default_locator = new WactTestTemplateLocator($this->default_config);
    $this->initWactDictionaries();
  }

  function initTemplate($file_name)
  {
    return new WactTemplate($file_name, $this->default_config, $this->default_locator);
  }

  public function registerTestingTemplate($file_path, $template, $file_name = '') {
    $this->default_locator->registerTestingTemplate($file_path, $template, $file_name);
  }

  function tearDown()
  {
    $this->default_locator->clearTestingTemplates();
  }

  function initWactDictionaries()
  {
    WactDictionaryHolder :: initialize($this->default_config);
  }
}


