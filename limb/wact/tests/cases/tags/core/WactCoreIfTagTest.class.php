<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    wact
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
?>