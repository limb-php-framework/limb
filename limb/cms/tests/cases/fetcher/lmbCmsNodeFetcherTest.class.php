<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsNodeFetcherTest.class.php 4989 2007-02-08 15:35:27Z pachanga $
 * @package    cms
 */
lmb_require('limb/cms/src/fetcher/lmbCmsNodeFetcher.class.php');

class lmbCmsNodeFetcherTest extends lmbCmsTestCase
{
  function _cleanUp()
  {
    $this->db->delete('class_name');
    $this->db->delete('node');
  }

  function testFetchByNodeId()
  {
    $root = $this->_createNode('root');
    $folder1 =$this->_createNode('folder1', $root);
    $folder2 =$this->_createNode('folder2', $root);
    $child1 =$this->_createNode('child1', $folder1);

    $fetcher = new lmbCmsNodeFetcher();
    $fetcher->setNodeId($folder1->getId());
    $nodes = $fetcher->getDataset();
    $this->assertEqual($nodes->count(), 1);
    $nodes->rewind();
    $node = $nodes->current();
    $this->assertEqual($node->getTitle(), $folder1->getTitle());
  }

  function testFetchByPath()
  {
    $root = $this->_createNode('root');
    $folder1 =$this->_createNode('folder1', $root);
    $folder2 =$this->_createNode('folder2', $root);
    $child1 =$this->_createNode('child1', $folder1);

    $fetcher = new lmbCmsNodeFetcher();
    $fetcher->setPath('/root/folder1');
    $nodes = $fetcher->getDataset();
    $this->assertEqual($nodes->count(), 1);
    $nodes->rewind();
    $node = $nodes->current();
    $this->assertEqual($node->getTitle(), $folder1->getTitle());
  }

  function testFetchByRequestIdIfNotPathNoIdSpecified()
  {
    $root = $this->_createNode('root');
    $folder1 =$this->_createNode('folder1', $root);
    $folder2 =$this->_createNode('folder2', $root);
    $child1 =$this->_createNode('child1', $folder1);

    $this->request->set('id', $folder1->getId());

    $fetcher = new lmbCmsNodeFetcher();
    $nodes = $fetcher->getDataset();
    $this->assertEqual($nodes->count(), 1);
    $nodes->rewind();
    $node = $nodes->current();
    $this->assertEqual($node->getTitle(), $folder1->getTitle());
  }
}

?>
