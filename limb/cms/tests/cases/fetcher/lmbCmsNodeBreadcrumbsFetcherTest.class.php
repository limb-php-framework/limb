<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cms/src/fetcher/lmbCmsNodeBreadcrumbsFetcher.class.php');

class lmbCmsNodeBreadcrumbsFetcherTest extends lmbCmsTestCase
{
  function _cleanUp()
  {
    $this->db->delete('class_name');
    $this->tree->deleteAll();
  }

  function testFetch()
  {
    $root = $this->_createNode('root');
    $folder1 =$this->_createNode('folder1', $root);
    $folder2 =$this->_createNode('folder2', $root);
    $child1 =$this->_createNode('child1', $folder1);
    $child2 =$this->_createNode('child2', $folder1);
    $child3 =$this->_createNode('child3', $folder2);

    $this->request->getUri()->reset('http://my.domain/root/folder1/child2');

    $fetcher = new lmbCmsNodeBreadcrumbsFetcher();

    $crumbs = $fetcher->getDataset();

    $this->assertEqual($crumbs->count(), 3);
    $crumbs->rewind();
    $kid = $crumbs->current();
    $this->assertEqual($kid->title, $root->title);
    $this->assertEqual($kid->getUrlPath(), '/root');
    $crumbs->next();
    $kid = $crumbs->current();
    $this->assertEqual($kid->title, $folder1->title);
    $this->assertEqual($kid->getUrlPath(), '/root/folder1');
    $crumbs->next();
    $kid = $crumbs->current();
    $this->assertEqual($kid->title, $child2->title);
    $this->assertEqual($kid->getUrlPath(), '/root/folder1/child2');
    $this->assertTrue($kid->is_last);
  }

  function testFetchForNonExistingNode()
  {
    $this->request->getUri()->reset('http://my.domain/root/folder1/child2');

    $fetcher = new lmbCmsNodeBreadcrumbsFetcher();

    $crumbs = $fetcher->getDataset();
    $this->assertEqual($crumbs->count(), 0);
  }
}

?>
