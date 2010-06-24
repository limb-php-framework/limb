<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @filter i18n_capitalize
 * @package i18n
 * @version $Id$
 */
class lmbI18NMacroCapitalizeFilter extends lmbMacroFilter
{
  function getValue()
  {
    $value = $this->base->getValue();

    return 'lmb_ucfirst('. $value . ')';
  }
}


