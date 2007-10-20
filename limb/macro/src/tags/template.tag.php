<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/lmbMacroTag.class.php');

/**
 * @tag template
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplateTag extends lmbMacroTag
{
  function generateContents($code)
  {
    $name = $this->get('name');

    $args = $code->getTempVarRef();
    $code->beginMethod('_template'. $name, array($args . '= array()'));
    $code->writePHP("if($args) extract($args);");
    parent :: generateContents($code);
    $code->endMethod();
  }
}

