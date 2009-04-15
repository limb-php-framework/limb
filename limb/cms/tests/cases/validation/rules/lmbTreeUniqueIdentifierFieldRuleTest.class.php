<?php

lmb_require('limb/cms/tests/cases/lmbCmsTestCase.class.php');
lmb_require('limb/validation/src/lmbErrorList.class.php');
require ('limb/cms/src/validation/rule/lmbTreeUniqueIdentifierRule.class.php');

Mock::generate('lmbErrorList', 'MockErrorList');

class lmbTreeUniqueIdentifierFieldRuleTest extends lmbCmsTestCase
{
  protected $error_list; 
  protected $tables_to_cleanup = array('lmb_cms_document');

  function setUp()
  {
    parent :: setUp();

    $this->error_list = new MockErrorList();
    $this->_initCmsDocumentTable();
  }

  function testValidWithoutSettingParentId()
  { 

    $saved_document = $this->_createDocument($identifier = 'test');
    $new_document = $this->_generateDocument($identifier = 'test2');

    $rule = new lmbTreeUniqueIdentifierRule('identifier', $new_document, 'документ с таким идентификатором уже существует');

    $this->error_list->expectNever('addError');

    $rule->validate($new_document, $this->error_list);
  }

  function testNotValidWithoutSettingParentId()
  {
    $saved_document = $this->_createDocument($identifier = 'test');
    $new_document = $this->_generateDocument($identifier = 'test');

    $rule = new lmbTreeUniqueIdentifierRule('identifier', $new_document, 'документ с таким идентификатором уже существует');

    $this->error_list->expectOnce('addError');

    $rule->validate($new_document, $this->error_list);
  }

  function testValidWithSettingParentId()
  {
    $saved_document1 = $this->_createDocument($identifier = 'test');
    $saved_document2 = $this->_createDocument($identifier = 'test2', $parent_document = $saved_document1);
    $new_document = $this->_generateDocument($identifier = 'test3', $parent_document = $saved_document1);

    $rule = new lmbTreeUniqueIdentifierRule('identifier', $new_document, 'документ с таким идентификатором уже существует', $saved_document1->getId());

    $this->error_list->expectNever('addError');

    $rule->validate($new_document, $this->error_list);

  }

  function testNotValidWithSettingParentId()
  {
    $saved_document1 = $this->_createDocument($identifier = 'test');
    $saved_document2 = $this->_createDocument($identifier = 'test2', $parent_document = $saved_document1);
    $new_document = $this->_generateDocument($identifier = 'test2', $parent_document = $saved_document1);

    $rule = new lmbTreeUniqueIdentifierRule('identifier', $new_document, 'документ с таким идентификатором уже существует', $saved_document1->getId());

    $this->error_list->expectOnce('addError');

    $rule->validate($new_document, $this->error_list);

  }


  /* function for just create an object of lmbCmsDocument but do not save it into DB */
  protected function _generateDocument($identifier, $parent_document = false)
  {
    $document = new lmbCmsDocument();

    $document->setIdentifier($identifier);
    $document->setTitle('title_'.microtime(true));
    $document->setContent('content_'.microtime(true));

    if(!$parent_document)
      $parent_document = lmbCmsDocument::findRoot();
    $document->setParent($parent_document);

    return $document;
  }
}