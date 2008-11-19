<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroWordDeclensionFilterTest extends lmbBaseMacroTest
{
  function _getPeopleResultForNumber($number)
  {
    $code = '{$#number|declension:"человек","человек","человека"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('number', $number);
    $out = $tpl->render();
    return $out;
  }
  
  function _getUserResultForNumber($number)
  {
    $code = '{$#number|declension:"пользователь", "пользователей", "пользователя"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('number', $number);
    $out = $tpl->render();
    return $out;
  }
  
  function testFunction()
  {
    $this->assertEqual($this->_getPeopleResultForNumber(1), "человек");
    $this->assertEqual($this->_getPeopleResultForNumber(2), "человека");
    $this->assertEqual($this->_getPeopleResultForNumber(3), "человека");
    $this->assertEqual($this->_getPeopleResultForNumber(4), "человека");
    $this->assertEqual($this->_getPeopleResultForNumber(5), "человек");
    $this->assertEqual($this->_getPeopleResultForNumber(13), "человек");
    $this->assertEqual($this->_getPeopleResultForNumber(16), "человек");    
    $this->assertEqual($this->_getPeopleResultForNumber(23), "человека");
    $this->assertEqual($this->_getPeopleResultForNumber(25), "человек");    
    $this->assertEqual($this->_getPeopleResultForNumber(100), "человек");
    $this->assertEqual($this->_getPeopleResultForNumber(103), "человека");
    $this->assertEqual($this->_getPeopleResultForNumber(113), "человек");
    $this->assertEqual($this->_getPeopleResultForNumber(123), "человека");
    $this->assertEqual($this->_getPeopleResultForNumber('123'), "человека");
    $this->assertEqual($this->_getPeopleResultForNumber(125), "человек");

    $this->assertEqual($this->_getUserResultForNumber(1), "пользователь");
    $this->assertEqual($this->_getUserResultForNumber(2), "пользователя");
    $this->assertEqual($this->_getUserResultForNumber(12), "пользователей");
    $this->assertEqual($this->_getUserResultForNumber(22), "пользователя");
    $this->assertEqual($this->_getUserResultForNumber(235), "пользователей");
    $this->assertEqual($this->_getUserResultForNumber(10001), "пользователь");    
  }
}

?>