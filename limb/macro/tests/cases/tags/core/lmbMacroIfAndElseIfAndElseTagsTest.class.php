<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroIfAndElseIfAndElseTagsTest extends lmbBaseMacroTest
{
  function testIfTag()
  {
    $template = '{{if var="$#foo"}}A{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $page->set('foo', true);
    $this->assertEqual($page->render(), 'A');

    $page->set('foo', false);
    $this->assertEqual($page->render(), '');
  }

  function testIfTag_AttrAlias()
  {
    $template = '{{if expr="$#foo"}}A{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $page->set('foo', true);
    $this->assertEqual($page->render(), 'A');

    $page->set('foo', false);
    $this->assertEqual($page->render(), '');
  }

  function testIfTag_MissedAttr()
  {
    $template = '{{if}}A{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');
    try
    {
      $page->render();
      $this->fail();
    }
    catch(lmbMacroException $e)
    {
      $this->pass();
    }   
  }

  function testElseIfTag()
  {
    $template = '{{if var="$#foo==1"}}A{{elseif var="$#foo==2"}}B{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $page->set('foo', 1);
    $this->assertEqual($page->render(), 'A');

    $page->set('foo', 2);
    $this->assertEqual($page->render(), 'B');
  }

  function testElseIfTag_WithoutIf()
  {
    $page = $this->_createMacroTemplate('{{elseif var="without_if"}}', 'tpl.html');
    try
    {
      $page->render();
      $this->fail();
    }
    catch(lmbMacroException $e)
    {
      $this->pass();
    }
  }

  function testElseIfTag_AttrAlias()
  {
    $template = '{{if var="$#foo==1"}}A{{elseif expr="$#foo==2"}}B{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $page->set('foo', 2);
    $this->assertEqual($page->render(), 'B');
  }

  function testElseIfTag_MissedAttr()
  {
    $template = '{{if}}A{{elseif}}{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');
    try
    {
      $page->render();
      $this->fail();
    }
    catch(lmbMacroException $e)
    {
      $this->pass();
    }   
  }


  function testElseTag()
  {
    $template = '{{if var="$#foo"}}A{{else}}B{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $page->set('foo', true);
    $this->assertEqual($page->render(), 'A');

    $page->set('foo', false);
    $this->assertEqual($page->render(), 'B');
  }

  function testElseTag_WithoutIf()
  {
    $page = $this->_createMacroTemplate('{{else}}', 'tpl.html');
    try
    {
      $page->render();
      $this->fail();
    }
    catch(lmbMacroException $e)
    {
      $this->pass();
    }
  }

  function testAcceptance()
  {
    $template = '{{if var="$#foo==1"}}A{{elseif var="$#foo==2"}}B{{else}}C{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $page->set('foo', 1);
    $this->assertEqual($page->render(), 'A');

    $page->set('foo', 2);
    $this->assertEqual($page->render(), 'B');

    $page->set('foo', 3);
    $this->assertEqual($page->render(), 'C');
  }
}

