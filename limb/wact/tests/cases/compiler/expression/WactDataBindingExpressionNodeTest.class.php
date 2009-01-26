<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';

Mock::Generate('WactCompileTreeNode', 'MockWactCompileTreeNode');
Mock::Generate('WactCompilerProperty', 'MockWactCompilerProperty');
Mock::Generate('WactCodeWriter', 'MockWactCodeWriter');

class WactCompilerPropertyDBETestVersion extends WactCompilerProperty
{
  function generateScopeEntry($code_writer)
  {
    $code_writer->writePHP('generateScopeEntryOutput');
  }

  function generatePreStatement($code_writer)
  {
    $code_writer->writePHP('generatePreStatementOutput');
  }

  function generateExpression($code_writer)
  {
    $code_writer->writePHP('generateExpressionOutput');
  }

  function generatePostStatement($code_writer)
  {
    $code_writer->writePHP('generatePostStatementOutput');
  }

  function generateScopeExit($code_writer)
  {
    $code_writer->writePHP('generateScopeExitOutput');
  }
}

class WactDataBindingExpressionNodeTest extends UnitTestCase
{
  function testAnalizeSimpleExpression()
  {
    $context = new WactCompileTreeNode();

    $DBE = new WactDataBindingExpressionNode('Test', $context);
    $DBE->prepare();
    $this->assertEqual($DBE->getFieldName(), 'Test');
  }

  function testAnalizeExpressionWithRootDatasourceSymbol()
  {
    $root = new WactCompileTreeRootNode();

    $context = new WactCompileTreeNode();
    $context->parent = $root;

    $DBE = new WactDataBindingExpressionNode('#Test', $context);
    $DBE->prepare();
    $this->assertEqual($DBE->getFieldName(), 'Test');
  }

  function testAnalizeExpressionWithParentDatasourceSymbol()
  {
    $parent = new WactCompileTreeRootNode();

    $context = new WactCompileTreeRootNode(); // use root node as a regular node.
    $context->parent = $parent;

    $DBE = new WactDataBindingExpressionNode('^Test', $context);
    $DBE->prepare();
    $this->assertEqual($DBE->getFieldName(), 'Test');
  }

  function testAnalizeExpressionWithTagPropertySymbolAndParentModifier()
  {
    $root = new WactCompileTreeRootNode();
    $root->registerProperty('Test', $property = new WactConstantProperty('value'));

    $context = new WactCompileTreeRootNode();
    $context->parent = $root;

    $DBE = new WactDataBindingExpressionNode('^:Test', $context);
    $DBE->prepare();
    $this->assertEqual($DBE->getValue(), 'value');
  }

  function testAnalizePathExpression()
  {
    $parent = new WactCompileTreeRootNode();

    $context = new WactCompileTreeRootNode(); // use root node as a regular node.
    $context->parent = $parent;

    $DBE = new WactDataBindingExpressionNode('Test.0', $context);
    $DBE->prepare();
    $this->assertEqual($DBE->getFieldName(), '0');
  }

  function testAnalizeExpressionWithChildModifier()
  {
    $parent = new WactCompileTreeRootNode();

    $child = new WactCompileTreeRootNode();
    $child->setServerId('child');
    $child->parent = $parent;

    $parent->addChild($child);

    $DBE = new WactDataBindingExpressionNode('[child]', $parent);
    $DBE->prepare();
    $this->assertNull($DBE->getFieldName());
    $this->assertReference($DBE->getDatasourceContext(), $child);
  }

  function testThrowExceptionIfDatasourceContextIsNotFound()
  {
    $location = new WactSourceLocation('my_file', 10);
    $context = new WactCompileTreeNode($location);

    $DBE = new WactDataBindingExpressionNode('^Test', $context);
    try
    {
      $DBE->prepare();
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Expression datasource context not found/', $e->getMessage());
      $this->assertEqual($e->getParam('file'), 'my_file');
      $this->assertEqual($e->getParam('line'), 10);
    }
  }

  function testBadExpression()
  {
    $expression = '&$|%';

    $context = new WactCompileTreeNode(new WactSourceLocation('my_file', 10));

    $DBE = new WactDataBindingExpressionNode($expression, $context);
    try
    {
      $DBE->prepare();
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Invalid data binding/', $e->getMessage());
      $this->assertEqual($e->getParam('expression'), $expression);
      $this->assertEqual($e->getParam('file'), 'my_file');
      $this->assertEqual($e->getParam('line'), 10);
    }
  }

  function testGetValueUnresolvedBinding()
  {
    $expression = 'Test';

    $context = new WactCompileTreeRootNode(new WactSourceLocation('my_file', 10));

    $DBE = new WactDataBindingExpressionNode('Test', $context);
    $this->assertFalse($DBE->isConstant());
    try
    {
      $DBE->getValue('Test');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Cannot resolve data binding/', $e->getMessage());
      $this->assertEqual($e->getParam('expression'), $expression);
      $this->assertEqual($e->getParam('file'), 'my_file');
      $this->assertEqual($e->getParam('line'), 10);
    }
  }

  function testBingingIsAlwaysNotConstantForNonPropertyExpression()
  {
    $context = new WactCompileTreeNode();

    $DBE = new WactDataBindingExpressionNode('Test', $context);

    $this->assertFalse($DBE->isConstant());
  }

  function testBindingIsConstantForConstantProperty()
  {
    $property = new WactConstantProperty('hello');

    $context = new WactCompileTreeNode();
    $context->registerProperty('Test', $property);

    $DBE = new WactDataBindingExpressionNode('Test', $context);

    $this->assertTrue($DBE->isConstant());
  }

  function testBindingIsNotConstantForNonConstantProperty()
  {
    $property = new WactCompilerProperty();

    $context = new WactCompileTreeNode();
    $context->registerProperty('Test', $property);

    $DBE = new WactDataBindingExpressionNode('Test', $context);

    $this->assertFalse($DBE->isConstant());
  }

  function testGetValueForConstantProperty()
  {
    $property = new WactConstantProperty('hello');

    $context = new WactCompileTreeNode();
    $context->registerProperty('Test', $property);

    $DBE = new WactDataBindingExpressionNode('Test', $context);

    $this->assertIdentical($DBE->getValue(), 'hello');
  }

  function testPropertyPrefix()
  {
    $property = new WactConstantProperty('hello');

    $context = new WactCompileTreeNode();
    $context->registerProperty('Test', $property);

    $DBE = new WactDataBindingExpressionNode(':Test', $context);

    $this->assertIdentical($DBE->getValue(), 'hello');
  }

  function testGetValueWithRootModifier()
  {
    $property = new WactConstantProperty('hello');

    $context = new WactCompileTreeNode();
    $context->registerProperty('Test', $property);

    $DBE = new WactDataBindingExpressionNode('#Test', $context);

    $this->assertIdentical($DBE->getValue(), 'hello');
  }

  function testGetValueWithParentModifier()
  {
    $property = new WactConstantProperty('hello');

    $parent = new WactCompileTreeRootNode();
    $parent->registerProperty('Test', $property);

    $context = new WactCompileTreeRootNode();
    $context->parent = $parent;

    $DBE = new WactDataBindingExpressionNode('^Test', $context);

    $this->assertIdentical($DBE->getValue(), 'hello');
  }

  function testGetValueWithTwoParentModifiers()
  {
    $property = new WactConstantProperty('hello');

    $parent = new WactCompileTreeRootNode();
    $parent->registerProperty('Test', $property);

    $child = new WactCompileTreeRootNode();
    $child->parent = $parent;

    $context = new WactCompileTreeRootNode();
    $context->parent = $child;

    $DBE = new WactDataBindingExpressionNode('^^Test', $context);

    $this->assertIdentical($DBE->getValue(), 'hello');
  }

  function testGetValueWithContextModifiers()
  {
    $property = new WactConstantProperty('hello');

    $parent = new WactCompileTreeRootNode();

    $child1 = new WactCompileTreeRootNode();
    $child1->setServerId('child1');
    $child1->parent = $parent;
    $parent->addChild($child1);

    $child2 = new WactCompileTreeRootNode();
    $child2->setServerId('child2');
    $child2->registerProperty('Test', $property);
    $child2->parent = $parent;
    $parent->addChild($child2);

    $context = new WactCompileTreeRootNode();
    $context->parent = $child1;

    $DBE = new WactDataBindingExpressionNode('#[child2]Test', $context);

    $this->assertIdentical($DBE->getValue(), 'hello');
  }

  function testGetValueWithSeveralContextModifiers()
  {
    $property = new WactConstantProperty('hello');

    $parent = new WactCompileTreeRootNode();

    $child1 = new WactCompileTreeRootNode();
    $child1->setServerId('child1');
    $child1->parent = $parent;
    $parent->addChild($child1);

    $child2 = new WactCompileTreeRootNode();
    $child2->setServerId('child2');
    $child2->parent = $parent;
    $parent->addChild($child2);

    $sub_child1 = new WactCompileTreeRootNode();
    $sub_child1->setServerId('sub_child1');
    $sub_child1->parent = $child2;
    $sub_child1->registerProperty('Test', $property);
    $child2->addChild($sub_child1);

    $context = new WactCompileTreeRootNode();
    $context->parent = $child1;

    $DBE = new WactDataBindingExpressionNode('#[child2][sub_child1]Test', $context);

    $this->assertIdentical($DBE->getValue(), 'hello');
  }

  function testGenerateCicleForPropertyExpression()
  {
    $code_writer = new WactCodeWriter();
    $property = new WactCompilerPropertyDBETestVersion();

    $context = new WactCompileTreeNode();
    $context->registerProperty('Test', $property);

    $DBE = new WactDataBindingExpressionNode('Test', $context);
    $DBE->generatePreStatement($code_writer);
    $DBE->generateExpression($code_writer);
    $DBE->generatePostStatement($code_writer);

    $this->assertEqual($code_writer->getCode(), '<?php generatePreStatementOutput'.
                                                 'generateExpressionOutput'.
                                                 'generatePostStatementOutput');
  }

  function testGenerateExpressionForRegularCompileTreeNode()
  {
    $code_writer = new WactCodeWriter();
    $root = new WactCompileTreeRootNode();

    $context = new MockWactCompileTreeNode();
    $context->setReturnReference('getDataSource', $root);

    $DBE = new WactDataBindingExpressionNode('Test', $context);
    $DBE->generateExpression($code_writer);

    $this->assertEqual($code_writer->getCode(), '<?php WactTemplate::getValue($root->datasource,\'Test\')');
  }

  function testGenerateExpressionWithRootModifier()
  {
    $code_writer = new WactCodeWriter();

    $root = new WactCompileTreeRootNode();
    $context = new WactCompileTreeNode();
    $context->parent = $root;

    $DBE = new WactDataBindingExpressionNode('#Test', $context);
    $DBE->generateExpression($code_writer);
    $this->assertEqual($code_writer->getCode(), '<?php WactTemplate::getValue($root->datasource,\'Test\')');
  }

  function testGenerateExpressionWithPointModifierUsedForArrayIndexOperations()
  {
    $code_writer = new WactCodeWriter();

    $root = new WactCompileTreeRootNode();
    $context = new WactCompileTreeNode();
    $context->parent = $root;

    $DBE = new WactDataBindingExpressionNode('.0', $context);
    $DBE->generateExpression($code_writer);
    $this->assertEqual($code_writer->getCode(), '<?php WactTemplate::getValue($root->datasource,0)');
  }

  function testGenerateExpressionWithLocalVariableModifier()
  {
    $code_writer = new WactCodeWriter();

    $root = new WactCompileTreeRootNode();
    $context = new WactCompileTreeNode();
    $context->parent = $root;

    $DBE = new WactDataBindingExpressionNode('$Test', $context);
    $DBE->generateExpression($code_writer);

    $this->assertEqual($code_writer->getCode(), '<?php $Test');
  }

  function testGenerateExpressionWithContextModifierAndNoFieldName()
  {
    $context = new WactCompileTreeRootNode();

    $child1 = new MockWactCompileTreeNode();
    $child1->setReturnValue('isDataSource', true);
    $child1->setReturnValue('getServerId', 'child1');
    $child1->setReturnValue('getComponentRefCode', '$components["A"]');

    $context->addChild($child1);

    $DBE = new WactDataBindingExpressionNode('[child1]', $context);
    $DBE->analyzeExpression();

    $this->assertFalse($DBE->isConstant());
    $this->assertFalse($DBE->getFieldName());

    $code_writer = new WactCodeWriter();

    $DBE->generateExpression($code_writer);

    $this->assertEqual($code_writer->getCode(), '<?php $components["A"]');
  }

  function testGenerateFullCicleForPathDBE()
  {
    $code_writer = new WactCodeWriter();

    $root = new WactCompileTreeRootNode();

    $context = new MockWactCompileTreeNode();
    $context->setReturnReference('getDataSource', $root);

    $DBE = new WactDataBindingExpressionNode('Test.item1.item2', $context);
    $DBE->generatePreStatement($code_writer);
    $DBE->generateExpression($code_writer);
    $DBE->generatePostStatement($code_writer);

    $this->assertEqual($code_writer->getCode(), '<?php $A= WactTemplate::getValue($root->datasource,\'Test\');'.
                                                '$B= WactTemplate::getValue($A,\'item1\');'.
                                                'WactTemplate::getValue($B,\'item2\')');
  }


  function testGenerateExpressionWithLocalVariableModifierWithPath()
  {
    $code_writer = new WactCodeWriter();

    $root = new WactCompileTreeRootNode();
    $context = new WactCompileTreeNode();
    $context->parent = $root;

    $DBE = new WactDataBindingExpressionNode('$Test.var', $context);
    $DBE->generatePreStatement($code_writer);
    $DBE->generateExpression($code_writer);
    $DBE->generatePostStatement($code_writer);

    $this->assertEqual($code_writer->getCode(), '<?php WactTemplate::getValue($Test,\'var\')');
  }
}


