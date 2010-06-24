<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/cms/src/model/lmbCmsDocument.class.php');

class lmbCmsTestCase extends UnitTestCase
{
  protected $db;
  protected $conn;
  protected $tables_to_cleanup = array('lmb_cms_seo');
  protected $identifier = 0;

  function setUp()
  {
    parent :: setUp();

    $this->conn = lmbToolkit::instance()->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);
    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
    $this->conn->disconnect();
    parent :: tearDown();
  }

  protected function _initCmsDocumentTable()
  {
    $table = new lmbTableGateway('lmb_cms_document');
    $root = array(
      'parent_id' => 0,
      'identifier' => '',
      'uri' => '/'
    );
    $table->insert($root);
  }

  /**
   * @param string $identifier
   * @return lmbCmsDocument
   */
  function _createDocument($identifier = false, $parent_document = false)
  {
    $this->identifier++;
    if(!$identifier)
      $identifier = 'identifier_' . microtime(true) . $this->identifier;

    $document = new lmbCmsDocument();
    $document->setIdentifier($identifier);
    $document->setTitle('title_'.microtime(true));
    $document->setContent('content_'.microtime(true));

    if(!$parent_document)
      $parent_document = lmbCmsDocument::findRoot();
    $document->setParent($parent_document);

    $document->save();
    return $document;
  }

  protected function _cleanUp()
  {
    foreach($this->tables_to_cleanup as $table_name)
      $this->db->delete($table_name);

    if(in_array('lmb_cms_document', $this->tables_to_cleanup))
      $this->_initCmsDocumentTable();
  }
}

?>
