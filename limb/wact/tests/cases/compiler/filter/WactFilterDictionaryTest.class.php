<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactFilterDictionaryTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/src/compiler/filter/WactFilterDictionary.class.php');

class WactFilterDictionaryTest extends UnitTestCase
{
  function setUp()
  {
    $this->dict = new WactFilterDictionary();
    $this->WactFilterInfo = new WactFilterInfo('TEST', 'Class');
    $this->dict->registerFilterInfo($this->WactFilterInfo, $file = 'whatever');
  }

  function testgetFilterInfo()
  {
    $this->assertIsA($this->dict->getFilterInfo('TEST'), 'WactFilterInfo');
  }

  function testRegisterFilterInfoOnceOnly()
  {
    $dictionary = new WactFilterDictionary();
    $info1 = new WactFilterInfo('test', 'Class');
    $info2 = new WactFilterInfo('test', 'Class');
    $dictionary->registerFilterInfo($info1, $file1 = 'whaever1');
    $dictionary->registerFilterInfo($info2, $file2 = 'whaever2');

    $this->assertEqual($dictionary->getFilterInfo('test'), $info1);
  }
}
?>