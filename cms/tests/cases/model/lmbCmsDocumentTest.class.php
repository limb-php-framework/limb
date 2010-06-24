<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cms/src/model/lmbCmsDocument.class.php');
lmb_require('limb/cms/tests/cases/lmbCmsTestCase.class.php');

class lmbCmsDocumentTest extends lmbCmsTestCase
{
  protected $tables_to_cleanup = array('lmb_cms_document');    

  function testGetUri()
  {
    $parent = $this->_createDocument('parent');
    $child = $this->_createDocument('child', $parent);

    $this->assertEqual($parent->getUri(), '/parent');
    $this->assertEqual($child->getUri(), '/parent/child');
  }
}