<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

class WactButtonTagTest extends WactTemplateTestCase
{
  var $Control;

  function testGetServerIdWithId()
  {
    $template = '<form runat="server">
                    <button id="test" name="foo" runat="server"></button>
                </form>';
    $this->registerTestingTemplate('/tags/form/controls/button/getserveridwithid.html', $template);

    $page = $this->initTemplate('/tags/form/controls/button/getserveridwithid.html');
    $page->findChild('test');
    $this->assertNoErrors();
  }

  function testGetServerIdWithName()
  {
    $template = '<form runat="server">
                    <button name="test" runat="server"></button>
                 </form>';
    $this->registerTestingTemplate('/tags/form/controls/button/getserveridwithname.html', $template);

    $page = $this->initTemplate('/tags/form/controls/button/getserveridwithname.html');
    $page->findChild('test');
    $this->assertNoErrors();
  }

  function testGetServerIdWithNameArray()
  {
    $template = '<form runat="server">
                    <button name="test[]" runat="server"></button>
                 </form>';
    $this->registerTestingTemplate('/tags/form/controls/button/getserveridwithnamearray.html', $template);

    $page = $this->initTemplate('/tags/form/controls/button/getserveridwithnamearray.html');
    $page->findChild('test');
    $this->assertNoErrors();
  }

  function testErrorClass()
  {
    $template = '<form runat="server">
                    <button id="test" errorclass="warning" runat="server"></button>
                 </form>';
    $this->registerTestingTemplate('/tags/form/controls/button/errorclass.html', $template);

    $page = $this->initTemplate('/tags/form/controls/button/errorclass.html');
    $button =  $page->findChild('test');
    $button->setError();
    $this->assertEqual('warning',$button->getAttribute('class'));
  }

  function testErrorStyle()
  {
    $template = '<form runat="server">
                    <button id="test" errorstyle="warning" runat="server"></button>
                 </form>';
    $this->registerTestingTemplate('/tags/form/controls/button/errorstyle.html', $template);

    $page = $this->initTemplate('/tags/form/controls/button/errorstyle.html');
    $button =  $page->findChild('test');
    $button->setError();
    $this->assertEqual('warning',$button->getAttribute('style'));
  }

  function testDisplayName()
  {
    $template = '<form runat="server">
                    <button id="test" displayname="my Button" runat="server"></button>
                 </form>';
    $this->registerTestingTemplate('/tags/form/controls/button/displayname.html', $template);

    $page = $this->initTemplate('/tags/form/controls/button/displayname.html');
    $button =  $page->findChild('test');
    $this->assertEqual('my Button',$button->getDisplayName());
  }

  function testCheckNestingLevelSelfNesting()
  {
    $template = '<form runat="server">
                    <button runat="server" id="parent">
                        <button id="test" runat="server"></button>
                    </button>
                 </form>';
    $this->registerTestingTemplate('/tags/form/controls/button/checknestinglevelselfnesting.html', $template);
    try
    {
      $page = $this->initTemplate('/tags/form/controls/button/checknestinglevelselfnesting.html');
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Tag cannot be nested within the same tag/', $e->getMessage());
    }
  }

  function testCheckNestingLevelNoForm()
  {
    $template = '<button id="test" name="my_Button3" runat="server"></button>';
    $this->registerTestingTemplate('/tags/form/controls/button/checknestinglevelnoform.html', $template);
    try
    {
      $page =  $this->initTemplate('/tags/form/controls/button/checknestinglevelnoform.html');
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/MISSINGENCLOSURE/', $e->getMessage());
    }
  }

  function testCompilerAttribuites()
  {
    $template = '<form runat="server">
                    <button id="test" errorstyle="x"
                        errorclass="y" displayname="z" runat="server"></button>
                 </form>';
    $this->registerTestingTemplate('/tags/form/controls/button/compilerattributes.html', $template);
    $page =  $this->initTemplate('/tags/form/controls/button/compilerattributes.html');

    $button =  $page->findChild('test');

    $this->assertFalse($button->hasAttribute('errorstyle'));
    $this->assertFalse($button->hasAttribute('errorclass'));
    $this->assertFalse($button->hasAttribute('displayname'));
  }

  // TESTS BELOW ARE REALLY TESTS OF THE RUNTIME COMPONENT
  function testDisplayNameWithTitle()
  {
    $template = '<form runat="server">
                    <button id="test" title="my Button1"
                        alt="my Button2" name="my_Button3" runat="server"></button>
                 </form>';
    $this->registerTestingTemplate('/tags/form/controls/button/displaynamewithtitle.html', $template);

    $page = $this->initTemplate('/tags/form/controls/button/displaynamewithtitle.html');
    $button =  $page->findChild('test');
    $this->assertEqual('my Button1', $button->getDisplayName());
  }

  function testDisplayNameWithAlt()
  {
    $template = '<form runat="server">
                    <button id="test"
                        alt="my Button2" name="my_Button3" runat="server"></button>
                 </form>';
    $this->registerTestingTemplate('/tags/form/controls/button/displaynamewithalt.html', $template);

    $page = $this->initTemplate('/tags/form/controls/button/displaynamewithalt.html');
    $button =  $page->findChild('test');
    $this->assertEqual('my Button2',$button->getDisplayName());
  }

  function testDisplayNameWithName()
  {
    $template = '<form runat="server">
                    <button id="test" name="my_Button3" runat="server"></button>
                 </form>';
    $this->registerTestingTemplate('/tags/form/controls/button/displaynamewithname.html', $template);

    $page = $this->initTemplate('/tags/form/controls/button/displaynamewithname.html');
    $button =  $page->findChild('test');
    $this->assertEqual('my Button3',$button->getDisplayName());
  }
}

