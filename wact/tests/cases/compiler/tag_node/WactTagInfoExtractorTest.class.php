<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/compiler/templatecompiler.inc.php');

class MyStubExtractorTag extends WactCompilerTag
{
}

class WactTagInfoExtractorTest extends UnitTestCase
{
  function testWactTagInfoCreation()
  {
    WactCompiler :: writeFile($file = WACT_CACHE_DIR . '/tag_extractor_file.tag.php', $context = 'whatever');

    $dictionary = new WactTagDictionary();
    $extractor = new WactTagInfoExtractor($dictionary, $file);

    $extractor->annotation('tag', 'my_tag');
    $extractor->annotation('suppress_attributes', 'attr1 attr2   attr3');
    $extractor->annotation('runat', 'client');
    $extractor->annotation('runat_as', 'WactFormTag');
    $extractor->annotation('req_const_attributes', 'attr1 attr2');
    $extractor->annotation('req_attributes', 'attr3');
    $extractor->annotation('parent_tag_class', 'WactFormTag');
    $extractor->annotation('restrict_self_nesting', null);
    $extractor->annotation('forbid_parsing', null);
    $extractor->annotation('forbid_end_tag', null);

    $extractor->beginClass('MyStubExtractorTag', 'CompilerTag');

    $tag_info = $dictionary->getWactTagInfo('my_tag');
    $this->assertEqual($tag_info->Tag, 'my_tag');
    $this->assertEqual($tag_info->TagClass, 'MyStubExtractorTag');
    $this->assertEqual($tag_info->SuppressAttributes, array('attr1', 'attr2', 'attr3'));
    $this->assertEqual($tag_info->RequiredAttributes, array('attr3'));
    $this->assertEqual($tag_info->RequiredConstantAttributes, array('attr1', 'attr2'));
    $this->assertEqual($tag_info->ParentTagClass, 'WactFormTag');
    $this->assertEqual($tag_info->RestrictSelfNesting, true);
    $this->assertEqual($tag_info->Runat, 'client');
    $this->assertEqual($tag_info->RunatAs, 'WactFormTag');
    $this->assertEqual($tag_info->ForbidParsing, true);
    $this->assertEqual($tag_info->ForbidEndTag, true);
    $this->assertEqual($tag_info->File, $file);

    unlink(WACT_CACHE_DIR . '/tag_extractor_file.tag.php');
  }

  function testExceptionIfFileNotFound()
  {
    $file = 'my_file';
    $dictionary = new WactTagDictionary();
    $extractor = new WactTagInfoExtractor($dictionary, $file);
    $extractor->annotation('tag', 'my_tag');
    try
    {
      $extractor->beginClass('MyStubExtractorTag', 'CompilerTag');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/File not found/', $e->getMessage());
      $this->assertEqual($e->getParam('file'), $file);
    }
  }

  function testAnnotationTagIsRequired()
  {
    WactCompiler :: writeFile($file = WACT_CACHE_DIR . '/tag_extractor_file.tag.php', $context = 'whatever');

    $dictionary = new WactTagDictionary();
    $extractor = new WactTagInfoExtractor($dictionary, $file);

    try
    {
      $extractor->beginClass('MyStubExtractorTag', 'CompilerTag');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Annotation not found in file/', $e->getMessage());
      $this->assertEqual($e->getParam('file'), $file);
      $this->assertEqual($e->getParam('annotation'), 'tag');
    }

    unlink(WACT_CACHE_DIR . '/tag_extractor_file.tag.php');
  }

  function testAllowRegiserTagAliases()
  {
    WactCompiler :: writeFile($file = WACT_CACHE_DIR . '/tag_extractor_file.tag.php', $context = 'whatever');

    $dictionary = new WactTagDictionary();
    $extractor = new WactTagInfoExtractor($dictionary, $file);

    $extractor->annotation('tag', 'my_tag, my_super_tag');
    $extractor->annotation('runat', 'server');

    $extractor->beginClass('MyStubExtractorTag', 'CompilerTag');

    $tag_info1 = $dictionary->getWactTagInfo('my_tag');
    $this->assertEqual($tag_info1->Runat, 'server');

    $tag_info2 = $dictionary->getWactTagInfo('my_super_tag');
    $this->assertEqual($tag_info2->Runat, 'server');

    unlink(WACT_CACHE_DIR . '/tag_extractor_file.tag.php');
  }
}

