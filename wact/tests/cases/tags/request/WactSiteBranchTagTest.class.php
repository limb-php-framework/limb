<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactSiteBranchTagTest extends WactTemplateTestCase
{
  protected $old_request_uri = '';

  function setUp()
  {
    parent :: setUp();
    if(isset($_SERVER["REQUEST_URI"]))
      $this->old_request_uri = $_SERVER["REQUEST_URI"];
  }

  function tearDown()
  {
    if($this->old_request_uri)
      $_SERVER["REQUEST_URI"] = $this->old_request_uri;

    parent :: tearDown();
  }

  function testRenderOneSection()
  {
    $template = '<site_branch_selector>'.
                  '<site_branch path="/ru\*">Russian</site_branch>'.
                  '<site_branch path="/en\*">English</site_branch>'.
                  '<site_branch default="true">Default</site_branch>'.
                '</site_branch_selector>';

    $this->registerTestingTemplate('/tags/request/site_branch_defined_branch.html', $template);

    $page = $this->initTemplate('/tags/request/site_branch_defined_branch.html');

    $_SERVER["REQUEST_URI"] = '/ru/catalog';

    $this->assertEqual($page->capture(), 'Russian');

    $_SERVER["REQUEST_URI"] = '/en/catalog';

    $this->assertEqual($page->capture(), 'English');
  }

  function testRenderDefaultSection()
  {
    $template = '<site_branch_selector>'.
                  '<site_branch path="/ru\*">Russian</site_branch>'.
                  '<site_branch path="/en\*">English</site_branch>'.
                  '<site_branch default="true">Default</site_branch>'.
                '</site_branch_selector>';

    $this->registerTestingTemplate('/tags/request/site_branch_default_branch.html', $template);

    $page = $this->initTemplate('/tags/request/site_branch_default_branch.html');

    $_SERVER["REQUEST_URI"] = '/de/catalog';

    $this->assertEqual($page->capture(), 'Default');
  }
}

