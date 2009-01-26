<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/components/pager/WactPagerComponent.class.php');

class WactPagerComponentTest extends UnitTestCase
{
  protected $component;
  protected $request;
  protected $old_get;
  protected $old_server;

  function setUp()
  {
    parent :: setUp();

    $this->component = new WactPagerComponent('navigator');

    $this->old_get = $_GET;
    $this->old_server = $_SERVER;

    $_SERVER['REQUEST_URI'] = 'http://test.com';
    $_GET = array();
  }

  function tearDown()
  {
    $_GET = $this->old_get;
    $_SERVER = $this->old_server;

    parent :: tearDown();
  }

  function testReset()
  {
    $_GET[$this->component->getPagerId()] = 2;

    $this->component->setItemsPerPage(10);
    $this->component->setPagesPerSection(5);
    $this->component->setTotalItems(100);

    $this->component->prepare();

    $this->assertEqual($this->component->getDisplayedPage(), 2);
    $this->assertFalse($this->component->isDisplayedPage());
    $this->assertEqual($this->component->getPage(), 1);
    $this->assertEqual($this->component->getTotalPages(), 10);
    $this->assertEqual($this->component->getPagesPerSection(), 5);
    $this->assertTrue($this->component->hasMoreThanOnePage());
    $this->assertEqual($this->component->getSectionBeginPage(), 1);
    $this->assertEqual($this->component->getSectionEndPage(), 5);
    $this->assertTrue($this->component->hasNext());
    $this->assertTrue($this->component->hasPrev());
    $this->assertEqual($this->component->getDisplayedPageBeginItem(), 11);
    $this->assertEqual($this->component->getStartingItem(), 10);
    $this->assertEqual($this->component->getDisplayedPageEndItem(), 20);
  }

  function testResetTotalItemsZero()
  {
    $_GET[$this->component->getPagerId()] = 2;

    $this->component->setItemsPerPage(10);
    $this->component->setPagesPerSection(5);
    $this->component->setTotalItems(0);

    $this->component->prepare();

    $this->assertEqual($this->component->getDisplayedPage(), 1);
    $this->assertEqual($this->component->getPage(), 1);
    $this->assertTrue($this->component->isDisplayedPage());
    $this->assertEqual($this->component->getTotalPages(), 1);
    $this->assertFalse($this->component->hasMoreThanOnePage());
    $this->assertEqual($this->component->getSectionBeginPage(), 1);
    $this->assertEqual($this->component->getSectionEndPage(), 1);
    $this->assertFalse($this->component->hasNext());
    $this->assertFalse($this->component->hasPrev());
    $this->assertEqual($this->component->getDisplayedPageBeginItem(), 0);
    $this->assertEqual($this->component->getStartingItem(), 0);
    $this->assertEqual($this->component->getDisplayedPageEndItem(), 0);
  }

  function testNextPage()
  {
    $_GET[$this->component->getPagerId()] = 2;

    $this->component->setTotalItems(40);
    $this->component->setItemsPerPage(10);
    $this->component->setPagesPerSection(5);

    $this->component->prepare();

    $this->assertEqual($this->component->getPage(), 1);

    $this->assertTrue($this->component->nextPage());
    $this->assertTrue($this->component->isValid());

    $this->assertEqual($this->component->getPage(), 2);
  }

  function testNextPageOutOfBounds()
  {
    $_GET[$this->component->getPagerId()] = 2;

    $this->component->setTotalItems(40);
    $this->component->setItemsPerPage(10);

    $this->component->prepare();

    $this->assertTrue($this->component->nextPage());
    $this->assertTrue($this->component->isValid());

    $this->assertTrue($this->component->nextPage());
    $this->assertTrue($this->component->isValid());

    $this->assertTrue($this->component->nextPage());
    $this->assertTrue($this->component->isValid());

    $this->assertFalse($this->component->nextPage());
    $this->assertFalse($this->component->isValid());
  }

  function testSectionNumbers()
  {
    $_GET[$this->component->getPagerId()] = 2;

    $this->component->setTotalItems(40);
    $this->component->setItemsPerPage(3);
    $this->component->setPagesPerSection(10);

    $this->component->prepare();

    $this->component->nextPage();

    $this->assertEqual($this->component->getSection(), 1);
    $this->assertEqual($this->component->getSectionBeginPage(), 1);
    $this->assertEqual($this->component->getSectionEndPage(), 10);
  }

  function testSectionNumbersRightBound()
  {
    $_GET[$this->component->getPagerId()] = 2;

    $this->component->setTotalItems(40);
    $this->component->setItemsPerPage(10);// 4 pages total
    $this->component->setPagesPerSection(10);

    $this->component->prepare();

    $this->component->nextPage();

    $this->assertEqual($this->component->getSection(), 1);
    $this->assertEqual($this->component->getSectionBeginPage(), 1);
    $this->assertEqual($this->component->getSectionEndPage(), 4);
  }

  function testNextSection()
  {
    $_GET[$this->component->getPagerId()] = 2;

    $this->component->setTotalItems(40);
    $this->component->setItemsPerPage(5);
    $this->component->setPagesPerSection(2);

    $this->component->prepare();

    $this->assertTrue($this->component->nextSection());
    $this->assertTrue($this->component->nextSection());
    $this->assertTrue($this->component->nextSection());
    $this->assertFalse($this->component->nextSection());
  }

  function testGetFirstPageUri()
  {
    $_GET['p1'] = ' wow ';
    $_GET['p2'] = array('3' => 'yo');

    $this->component->setPagerPrefix('p');
    $this->component->prepare();

    $uri = $this->component->getPageUri(1);

    $this->assertEqual($uri, 'http://test.com?p1=+wow+&p2[3]=yo');
  }

  function testGetFirstPageUriNoQuery()
  {
    $this->component->setPagerPrefix('p');
    $this->component->prepare();

    $uri = $this->component->getPageUri(1);

    $this->assertEqual($uri, 'http://test.com');
  }

  function testGetPageUri()
  {
    $_GET['p1'] = 'wow';
    $_GET['p2'] = array('3' => ' yo ');

    $this->component->setPagerPrefix('p');
    $this->component->prepare();

    $uri = $this->component->getPageUri(2);

    $this->assertEqual($uri, 'http://test.com?p1=wow&p2[3]=+yo+&p_navigator=2');
  }

  function testGetPrevSectionUri()
  {
    $this->component->setPagerPrefix('p');
    $this->component->setTotalItems(60);
    $this->component->setItemsPerPage(10);
    $this->component->setPagesPerSection(2);

    $_GET['p_navigator'] = 3;

    $this->component->prepare();

    $this->component->nextPage();

    $uri = $this->component->getSectionUri();

    $this->assertEqual($uri, 'http://test.com?p_navigator=2');
    $this->assertEqual($this->component->getSectionBeginPage(), 1);
    $this->assertEqual($this->component->getSectionEndPage(), 2);
  }

  function testGetNextSectionUri()
  {
    $_GET['p_navigator'] = 3;

    $this->component->setPagerPrefix('p');
    $this->component->setTotalItems(60);
    $this->component->setItemsPerPage(10);
    $this->component->setPagesPerSection(2);

    $this->component->prepare();

    for($i = 0; $i < 5; $i++)
      $this->component->nextPage();

    $uri = $this->component->getSectionUri(2);

    $this->assertEqual($uri, 'http://test.com?p_navigator=5');
    $this->assertEqual($this->component->getSectionBeginPage(), 5);
    $this->assertEqual($this->component->getSectionEndPage(), 6);
  }
}


