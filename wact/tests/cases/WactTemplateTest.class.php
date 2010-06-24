<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2009 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    wact
 */

require_once('limb/wact/src/WactTemplate.class.php');

class WactTemplateTest extends UnitTestCase
{
  function testEscapeCommonCase()
  {
    $this->assertEqual(WactTemplate :: escape('\'A\' & "B"'), '&#039;A&#039; &amp; &quot;B&quot;');
  }

  function testEscapeAlreadyTranslatedEntity()
  {
    $this->assertEqual(WactTemplate :: escape('&#8364;'), '&#8364;');
  }

  function testEscapeAlreadyTranslatedEntityWithNonNumericIndex()
  {
    $this->assertEqual(WactTemplate :: escape('&#x1234;'), '&#x1234;');
  }
}


