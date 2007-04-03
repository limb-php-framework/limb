<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactNodeBuilderTest.class.php 5203 2007-03-07 08:58:21Z serega $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';

Mock :: generate('WactHTMLParser', 'MockWactHTMLParser');
Mock :: generate('WactTreeBuilder', 'MockWactTreeBuilder');

class WactNodeBuilderTestTag extends WactCompilerTag{}

class WactNodeBuilderTestProperty extends WactCompilerProperty
{
  function isConstant()
  {
    return true;
  }

  function getValue()
  {
    return 'my_property_value';
  }
}

class WactNodeBuilderTestFilter extends WactCompilerFilter{}

class WactNodeBuilderTest extends UnitTestCase
{
  protected $tree_builder;
  protected $node_builder;
  protected $property_dictionary;
  protected $filter_dictionary;
  protected $locator;

  function setUp()
  {
    $this->tree_builder = new MockWactTreeBuilder();
    $this->property_dictionary = new WactPropertyDictionary();
    $this->filter_dictionary = new WactFilterDictionary();
    $this->node_builder = new WactNodeBuilder($this->tree_builder,
                                              $this->property_dictionary,
                                              $this->filter_dictionary);
    $this->locator = new MockWactHTMLParser();
    $this->node_builder->setDocumentLocator($this->locator);
  }

  function testBuildTagNodeWithPropertyAndAttributes()
  {
    $tag_info = new WactTagInfo('my_tag', 'WactNodeBuilderTestTag');

    $property_info = new WactPropertyInfo('my_property', 'WactNodeBuilderTestTag', 'WactNodeBuilderTestProperty');
    $this->property_dictionary->registerPropertyInfo($property_info, __FILE__);

    $attrs = array('attr1' => 'value1',
                   'attr2' => 'value2{$my_property}', // will be compount attribute
                   'attr3' => '{$my_property}', // will be regular attribute expression
                   );

    $node = $this->node_builder->buildTagNode($tag_info, $tag = 'MY_TAG', $attrs, $isEmpty = true);

    $this->assertIsA($node, 'WactNodeBuilderTestTag');
    $this->assertTrue($node->emptyClosedTag);
    $this->assertIsA($node->getProperty('my_property'), 'WactNodeBuilderTestProperty');

    $this->assertEqual($node->getAttribute('attr1'), 'value1');

    $attr2 = $node->getAttributeNode('attr2');
    $this->assertIsA($attr2, 'WactCompoundAttribute');

    $attr3 = $node->getAttributeNode('attr3');
    $this->assertIsA($attr3, 'WactAttributeExpression');

    $attr2_fragment1 = $attr2->getFragment(0);
    $this->assertEqual($attr2->getFragment(0)->getValue(), 'value2');
    $this->assertIsA($attr2->getFragment(1), 'WactAttributeExpression');
    $this->assertEqual($attr2->getFragment(1)->getValue(), 'my_property_value');
    $this->assertReference($attr2->getFragment(1)->getExpression()->getFilterDictionary(),
                           $this->filter_dictionary);

    $this->assertReference($attr3->getExpression()->getFilterDictionary(),
                           $this->filter_dictionary);
  }

  function testBuildTagNodeWithNullAttributeThrowsException()
  {
    $this->locator->expectCallCount('getCurrentLocation', 2);
    $location = new WactSourceLocation('my_file', 5);
    $this->locator->setReturnValue('getCurrentLocation', $location);

    $tag_info = new WactTagInfo('my_tag', 'WactNodeBuilderTestTag');

    $attrs = array('attr1' => null);

    try
    {
      $node = $this->node_builder->buildTagNode($tag_info, $tag = 'MY_TAG', $attrs, $isEmpty = true);
      $this->assertTrue(false);
    }
    catch (WactException $e)
    {
      $this->assertWantedPattern('/Attribute should have a value/', $e->getMessage());
      $this->assertEqual($e->getParam('file'), 'my_file');
      $this->assertEqual($e->getParam('line'), 5);
      $this->assertEqual($e->getParam('tag'), 'MY_TAG');
      $this->assertEqual($e->getParam('attribute'), 'attr1');
    }
  }

  function testAddContentWithSimpleText()
  {
    $this->locator = new MockWactHTMLParser();
    $this->locator->expectNever('getCurrentLocation');

    $text = 'my text';

    $this->tree_builder->expectOnce('addWactTextNode', array($text));

    $this->node_builder->addContent($text);
  }

  function testAddContentWithTextWithDBE()
  {
    $this->locator->expectOnce('getCurrentLocation');
    $location = new WactSourceLocation('my_file', 5);
    $this->locator->setReturnValue('getCurrentLocation', $location);

    $text = 'my text{$var}my other text';

    $this->tree_builder->expectArgumentsAt(0, 'addWactTextNode', array('my text'));
    $this->tree_builder->expectArgumentsAt(1, 'addWactTextNode', array('my other text'));

    $this->filter_dictionary->registerFilterInfo(new WactFilterInfo('html', 'WactNodeBuilderTestFilter'), __FILE__);

    $component = new WactCompileTreeNode(null);
    $expression = new WactExpression('var', $component, $this->filter_dictionary, 'html');
    $node = new WactOutputExpressionNode($location, $expression);

    $this->tree_builder->expectOnce('getCursor');
    $this->tree_builder->setReturnValue('getCursor', $component);

    $this->tree_builder->expectOnce('addNode', array($node));

    $this->node_builder->addContent($text);
  }
}
?>