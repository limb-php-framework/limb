<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/annotation/WactClassAnnotationParser.class.php');
require_once(dirname(__FILE__) . '/ListenerStub.class.php');

class PartialListener {
    function endClass(){}
}

Mock :: generate('PartialListener');

class WactClassAnnotationParserTest extends UnitTestCase
{
  protected $listener;

  function setUp() {
    $this->listener = new ListenerStub();
  }

  function testAnnotationWithTypeOnly()
  {
    $source = <<<EOD
<?php
/*
*  @param
*/
?>
EOD;
    $tokenizer = new WactClassAnnotationParser();
    $tokenizer->process($this->listener, $source);

    $this->assertEqual($this->listener->history, array(array('annotation', 'param', NULL)));
  }

  function testAnnotationWithTypeAndTitle()
  {
    $source = <<<EOD
<?php
/*
*  @param one
*  @param two
*/
?>
EOD;
    $tokenizer = new WactClassAnnotationParser();
    $tokenizer->process($this->listener, $source);

    $this->assertEqual($this->listener->history, array(array('annotation', 'param', 'one'),
                                                       array('annotation', 'param', 'two')));
  }

  function testPHP5DocStyleAnnotation() {
    $source = <<<EOD
<?php
/**
*  @param one
*  @param two
*/
?>
EOD;
    $tokenizer = new WactClassAnnotationParser();
    $tokenizer->process($this->listener, $source);

    $this->assertEqual($this->listener->history, array(array('annotation', 'param', 'one'),
                                                       array('annotation', 'param', 'two')));
  }

  function testIdentedAnnotation()
  {
    $source = <<<EOD
<?php
 /**
   *  @param one
  *  @param two
*/
?>
EOD;
    $tokenizer = new WactClassAnnotationParser();
    $tokenizer->process($this->listener, $source);

    $this->assertEqual($this->listener->history, array(array('annotation', 'param', 'one'),
                                                         array('annotation', 'param', 'two')));
  }

  function testGenericClassToken()
  {
    $source = <<<EOD
<?php

class Foo{}

?>
EOD;
    $tokenizer = new WactClassAnnotationParser();
    $tokenizer->process($this->listener, $source);

    $this->assertEqual($this->listener->history, array(array('beginClass', 'Foo', NULL),
                                                       array('endClass')));
  }

  function testChildClassToken()
  {
    $source = <<<EOD
<?php

class Foo extends Bar{}

?>
EOD;
    $tokenizer = new WactClassAnnotationParser();
    $tokenizer->process($this->listener, $source);

    $this->assertEqual($this->listener->history, array(array('beginClass', 'Foo', 'Bar'),
                                                       array('endClass')));
  }

  function testPropertyToken()
  {
    $source = <<<EOD
<?php

class Foo
{
    var \$null;
    var \$property = 'value';
}

?>
EOD;
    $tokenizer = new WactClassAnnotationParser();
    $tokenizer->process($this->listener, $source);

    $this->assertEqual($this->listener->history, array(array('beginClass', 'Foo', NULL),
                                                       array('property', 'null', 'public'),
                                                       array('property', 'property', 'public'),
                                                       array('endClass')));
  }

  function testMethodToken()
  {
    $source = <<<EOD
<?php

class Foo
{
  function Foo()
  {
    do{}while(1=1);
  }
}
?>
EOD;
    $tokenizer = new WactClassAnnotationParser();

    $tokenizer->process($this->listener, $source);

    $this->assertEqual($this->listener->history, array(array('beginClass', 'Foo', NULL),
                                                       array('method', 'Foo'),
                                                       array('endClass')));
  }

  function testMethodTokendAndIgnoreFunction()
  {
    $source = <<<EOD
<?php

class Foo
{
  function Foo(){}
}

function Ignore(){}

?>
EOD;
    $tokenizer = new WactClassAnnotationParser();

    $tokenizer->process($this->listener, $source);

    $this->assertEqual($this->listener->history, array(array('beginClass', 'Foo', NULL),
                                                       array('method', 'Foo'),
                                                       array('endClass')));
  }

  function testOnlyExistingMethodsOfListenerGetInvoked()
  {
    $source = <<<EOD
<?php

class Foo{}
?>
EOD;
    $listener = new MockPartialListener();
    $listener->expectOnce('endClass');

    $tokenizer = new WactClassAnnotationParser();
    $tokenizer->process($listener, $source);
  }
}

