<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');
lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');

class InterceptingFilterStub
{
  var $captured = array();
  var $run = false;

  function run($fc)
  {
    $this->run = true;
    $this->captured['filter_chain'] = $fc;

    $fc->next();
  }
}

class OutputFilter1
{
  function run($fc)
  {
    echo '<filter1>';
    $fc->next();
    echo '</filter1>';
  }
}

class OutputFilter2
{
  function run($fc)
  {
    echo '<filter2>';
    $fc->next();
    echo '</filter2>';
  }
}

class OutputFilter3
{
  function run($fc)
  {
    echo '<filter3>';
    $fc->next();
    echo '</filter3>';
  }
}

class lmbFilterChainTest extends UnitTestCase
{
  var $fc;
  function setUp()
  {
    $this->fc = new lmbFilterChain();
  }

  function testProcess()
  {
    $mock_filter = new InterceptingFilterStub();

    $this->fc->registerFilter($mock_filter);

    $this->assertFalse($mock_filter->run);

    $this->fc->process();

    $this->assertTrue($mock_filter->run);

    $this->assertIsA($mock_filter->captured['filter_chain'], 'lmbFilterChain');
  }

  function testProcessProperNesting()
  {
    $f1 = new OutputFilter1();
    $f2 = new OutputFilter2();

    $this->fc->registerFilter($f1);
    $this->fc->registerFilter($f2);

    ob_start();

    $this->fc->process();

    $str = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($str, '<filter1><filter2></filter2></filter1>');
  }

  function testFilterChainAsAFilter()
  {
    $f1 = new OutputFilter1();
    $f2 = new OutputFilter2();

    $fc = new lmbFilterChain();

    $fc1 = new lmbFilterChain();
    $fc1->registerFilter($f1);

    $fc2 = new lmbFilterChain();
    $fc2->registerFilter($f2);

    $fc->registerFilter($fc1);
    $fc->registerFilter($fc2);

    ob_start();

    $fc->process();

    $str = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($str, '<filter1></filter1><filter2></filter2>');
  }
}


