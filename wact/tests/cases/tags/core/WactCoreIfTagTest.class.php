<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactCoreIfTagTest extends WactTemplateTestCase
{
  function testConstantExpression()
  {
    $template = '<core:if exp=\'{$"Var"}\'>here</core:if>';

    $this->registerTestingTemplate('/tags/core/if/simple.html', $template);
    $page = $this->initTemplate('/tags/core/if/simple.html');
    $output = $page->capture();
    $this->assertEqual($output, 'here');
  }

  function testDBEExpression()
  {
    $template = '<core:if exp="{$Var}">here</core:if>';

    $this->registerTestingTemplate('/tags/core/if/dbe_expression.html', $template);
    $page = $this->initTemplate('/tags/core/if/dbe_expression.html');
    $page->set('Var', '1');
    $output = $page->capture();
    $this->assertEqual($output, 'here');
  }

  function testDBEExpressionFailed()
  {
    $template = '<core:if exp=\'{$Var}\'>here</core:if>';

    $this->registerTestingTemplate('/tags/core/if/dbe_expression_failed.html', $template);
    $page = $this->initTemplate('/tags/core/if/dbe_expression_failed.html');
    $output = $page->capture();
    $this->assertEqual($output, '');
  }

  function testDBEExpressionRoorModifier()
  {
    $template = '<core:datasource id="container1"><core:if exp="{$#[container1]Var}">here</core:if></core:datasource>';

    $this->registerTestingTemplate('/tags/core/if/dbe_root_modifier.html', $template);
    $page = $this->initTemplate('/tags/core/if/dbe_root_modifier.html');
    $page->setChildDatasource('container1', array('Var' => 1));
    $output = $page->capture();
    $this->assertEqual($output, 'here');
  }

  function testDBEExpressionPathBasedVariable()
  {
    $template = '<core:datasource id="container1"><core:if exp="{$#[container1]Var.subvar}">here</core:if></core:datasource>';

    $this->registerTestingTemplate('/tags/core/if/dbe_path_based_variable.html', $template);
    $page = $this->initTemplate('/tags/core/if/dbe_path_based_variable.html');
    $page->setChildDatasource('container1', array('Var' => array('subvar' => 1)));
    $output = $page->capture();
    $this->assertEqual($output, 'here');
  }

  function testComplexExpression()
  {
    $template = '<core:if exp="{$Var1 * 3 > Var2}">here</core:if>';

    $this->registerTestingTemplate('/tags/core/if/complex_expression.html', $template);
    $page = $this->initTemplate('/tags/core/if/complex_expression.html');
    $page->set('Var1', '2');
    $page->set('Var2', '4');
    $output = $page->capture();
    $this->assertEqual($output, 'here');
  }
}

