<?php
require_once('limb/view/src/wact/lmbWactTemplateConfig.class.php');
require_once('limb/wact/tests/cases/WactTestTemplateLocator.class.php');
require_once('limb/core/src/lmbCollection.class.php');
require_once('limb/wact/src/WactTemplate.class.php');
require_once('limb/wact/src/compiler/WactDictionaryHolder.class.php');
require_once('limb/validation/src/lmbErrorList.class.php');

class lmbWactTestCase extends UnitTestCase
{
  protected $toolkit;
  protected $locator;
  protected $config;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();

    $this->compiled_dir = LIMB_VAR_DIR . '/compiled';
    $this->config = new lmbWactTemplateConfig($this->compiled_dir);
    $this->locator = new WactTestTemplateLocator($this->config);

    $this->initWactDictionaries($this->config);
  }

  function initTemplate($template_file)
  {
    return new WactTemplate($template_file, $this->config, $this->locator);
  }

  function tearDown()
  {
    $this->locator->clearTestingTemplates();
    lmbToolkit :: restore();
  }

  function registerTestingTemplate($file, $template)
  {
    $this->locator->registerTestingTemplate($file, $template);
  }

  function initWactDictionaries($config)
  {
    WactDictionaryHolder :: initialize($config);
  }
}


