<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

class WactTemplateMathFilterTestCase extends WactTemplateTestCase {

  function testAdd()
  {
    $template = '{$val|math:"+3"}';

    $this->registerTestingTemplate('/template/filter/math_add.html', $template);
    $page = $this->initTemplate('/template/filter/math_add.html');
    $page->set('val',6);

    $output = $page->capture();
    $this->assertEqual($output, '9');
  }

  function testMult()
  {
    $template = '{$val|math:"*3"}';

    $this->registerTestingTemplate('/template/filter/math_mult.html', $template);
    $page = $this->initTemplate('/template/filter/math_mult.html');
    $page->set('val',6);

    $output = $page->capture();
    $this->assertEqual($output, '18');
  }

  function testDivVar()
  {
    $template = '<core:set exp="/{$val2}" />{$val|math:exp}';

    $this->registerTestingTemplate('/template/filter/math_divvar.html', $template);
    $page = $this->initTemplate('/template/filter/math_divvar.html');
    $page->set('val',14);
    $page->set('val2',8);

    $output = $page->capture();
    $this->assertEqual($output, '1.75');
  }

  function testDivVarLtOne()
  {
    //adds a number filter on output to eliminate leading 0 difference
    //between php4 and php5
    $template = '<core:set exp="/{$val2}" />{$val|math:exp|number:2}';

    $this->registerTestingTemplate('/template/filter/math_divvarlt1.html', $template);
    $page = $this->initTemplate('/template/filter/math_divvarlt1.html');
    $page->set('val',6);
    $page->set('val2',8);

    $output = $page->capture();
    $this->assertEqual($output, '0.75');
  }

  function testPctVar()
  {
    $template = '<core:set exp="/{$val2}*100" />{$val|math:exp|number:2}%';

    $this->registerTestingTemplate('/template/filter/math_pctvar.html', $template);
    $page = $this->initTemplate('/template/filter/math_pctvar.html');
    $page->set('val',63);
    $page->set('val2',85);

    $output = $page->capture();
    $this->assertEqual($output, '74.12%');
  }

  function testPctVar2Parm()
  {
    $template = '{$val}/{$val2}={$val|math:"/",val2,"*100"|number:3}%';

    $this->registerTestingTemplate('/template/filter/math_pctvar2parm.html', $template);
    $page = $this->initTemplate('/template/filter/math_pctvar2parm.html');
    $page->set('val',63);
    $page->set('val2',85);

    $output = $page->capture();
    $this->assertEqual($output, '63/85=74.118%');
  }

  function testBadExp()
  {
    $template = '<core:set exp="/bad*100" />{$val|math:exp|number:2}';

    $this->registerTestingTemplate('/template/filter/math_err.html', $template);
    $page = $this->initTemplate('/template/filter/math_err.html');
    $page->set('val',63);

    try
    {
      $output = $page->capture();
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Interal Error/i', $e->getMessage());
      $this->assertWantedPattern('/Undefined operator(?U).*bad/i', $e->getMessage());
    }
  }

  function testChainedFilter()
  {
    $template = '<list:list from="data"><list:item>{$v1|stats:"v1"} {$v2|stats:"v2"}<br /></list:item></list:list>'
      .'<core:set div_v2=\'/{$v2|stats:"v2","sum"}*100\' />'
      .'v1/v2*100={$v1|stats:"v1","sum"|math:div_v2|number:2}';

    $this->registerTestingTemplate('/template/filter/math_chain.html', $template);
    $page = $this->initTemplate('/template/filter/math_chain.html');
    $page->set('data',new WactArrayIterator(array(
      array('v1'=>100, 'v2'=>150)
      ,array('v1'=>200, 'v2'=>175)
      ,array('v1'=>170, 'v2'=>150)
      )));

    $output = $page->capture();
    $this->assertWantedPattern('/98[.]95$/', $output);
  }
}

