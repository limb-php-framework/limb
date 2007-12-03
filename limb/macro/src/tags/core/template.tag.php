<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag template
 * @req_attributes name
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplateTag extends lmbMacroTag
{
  function generate($code)
  {
    $name = $this->get('name');

    $args = $code->generateVar();
    $code->beginMethod('_template'. $name, array($args . '= array()'));
    $code->writePHP("if($args) extract($args);");
    parent :: generate($code);
    $code->endMethod();
  }
}

