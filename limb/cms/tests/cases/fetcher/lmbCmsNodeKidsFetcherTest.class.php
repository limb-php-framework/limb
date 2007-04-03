<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsNodeKidsFetcherTest.class.php 4989 2007-02-08 15:35:27Z pachanga $
 * @package    cms
 */
lmb_require('limb/cms/src/fetcher/lmbCmsNodeKidsFetcher.class.php');

class lmbCmsNodeKidsFetcherTest extends lmbCmsTestCase
{
  function _cleanUp()
  {
    $this->db->delete('class_name');
    $this->db->delete('node');
  }

  function testFetchByParentId()
  {
    $root = $this->_createNode('root');
    $folder1 =$this->_createNode('folder1', $root);
    $folder2 =$this->_createNode('folder2', $root);
    $child1 =$this->_createNode('child1', $folder1);
    $child2 =$this->_createNode('child2', $folder1);
    $child3 =$this->_createNode('child3', $folder2);

    $fetcher = new lmbCmsNodeKidsFetcher();
    $fetcher->setParentId($folder1->getId());
    $kids = $fetcher->getDataset();

    $this->assertEqual($kids->count(), 2);
    $kids->rewind();
    $kid = $kids->current();
    $this->assertEqual($kid->getTitle(), $child1->getTitle());
    $kids->next();
    $kid = $kids->current();
    $this->assertEqual($kid->getTitle(), $child2->getTitle());
  }

  function testFetchRootNodeIfNoParentId()
  {
    $root1 = $this->_createNode('root1');
    $root2 = $this->_createNode('root2');
    $folder1 =$this->_createNode('folder1', $root1);
    $folder2 =$this->_createNode('folder2', $root2);

    $fetcher = new lmbCmsNodeKidsFetcher();
    $kids = $fetcher->getDataset();

    $this->assertEqual($kids->count(), 2);
    $kids->rewind();
    $kid = $kids->current();
    $this->assertEqual($kid->getTitle(), $root1->getTitle());
    $kids->next();
    $kid = $kids->current();
    $this->assertEqual($kid->getTitle(), $root2->getTitle());
  }

  function testFetchByParentPath()
  {
    $root = $this->_createNode('root');
    $folder1 =$this->_createNode('folder1', $root);
    $folder2 =$this->_createNode('folder2', $root);
    $child1 =$this->_createNode('child1', $folder1);
    $child2 =$this->_createNode('child2', $folder1);
    $child3 =$this->_createNode('child3', $folder2);

    $fetcher = new lmbCmsNodeKidsFetcher();
    $fetcher->setParentPath('/root/folder1');
    $kids = $fetcher->getDataset();

    $this->assertEqual($kids->count(), 2);
    $kids->rewind();
    $kid = $kids->current();
    $this->assertEqual($kid->getTitle(), $child1->getTitle());
    $kids->next();
    $kid = $kids->current();
    $this->assertEqual($kid->getTitle(), $child2->getTitle());
  }

  function testFetchRestrictByController()
  {
    $root = $this->_createNode('root');
    $folder1 =$this->_createNode('folder1', $root, 'Controller_A');
    $folder2 =$this->_createNode('folder2', $root, 'Controller_B');

    $fetcher = new lmbCmsNodeKidsFetcher();
    $fetcher->setParentId($root->getId());
    $fetcher->setController('Controller_B');
    $kids = $fetcher->getDataset();

    $this->assertEqual($kids->count(), 1);
    $kids->rewind();
    $kid = $kids->current();
    $this->assertEqual($kid->getTitle(), $folder2->getTitle());
  }
}

?>
