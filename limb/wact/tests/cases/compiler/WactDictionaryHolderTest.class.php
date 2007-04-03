<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactDictionaryHolderTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/src/compiler/templatecompiler.inc.php');
require_once('limb/wact/tests/cases/WactTestTemplateConfig.class.php');

class WactDictionaryHolderTest extends UnitTestCase
{
  function tearDown()
  {
    WactDictionaryHolder :: resetInstance();
  }

  function testGetUninitializedDictionaryThrowsException()
  {
    $holder = new WactDictionaryHolder(null);
    try
    {
      $holder->getDictionary('filter');
      $this->assertTrue(false);
    }
    catch(Exception $e)
    {
    }
  }

  function testGetInitializedWactFilterDictionary()
  {
    $config = array('force_scan' => 0,
                    'scan_directories' => array(),
                    'cache_dir' => WACT_CACHE_DIR);

    $holder = new WactDictionaryHolder(new WactTestTemplateConfig($config));
    $holder->initializeWactFilterDictionary();
    $dictionary = $holder->getFilterDictionary();
    $this->assertIsA($dictionary, 'WactFilterDictionary');
  }

  function testGetInstance()
  {
    $config = array('force_scan' => 0,
                    'scan_directories' => array(),
                    'cache_dir' => WACT_CACHE_DIR);

    WactDictionaryHolder :: initialize(new WactTestTemplateConfig($config));

    $instance1 = WactDictionaryHolder :: instance();
    $instance1->initializeWactFilterDictionary();
    $dictionary1 = $instance1->getFilterDictionary();
    $this->assertIsA($dictionary1, 'WactFilterDictionary');

    $instance2 = WactDictionaryHolder :: instance();
    $dictionary2 = $instance1->getFilterDictionary();
    $this->assertReference($dictionary2, $dictionary1);
  }

  function testGetInstanceThrowsExceptionIfWasNotInitialized()
  {
    try
    {
      WactDictionaryHolder :: instance();
      $this->assertTrue(false);
    }
    catch(Exception $e)
    {
    }
  }
}
?>