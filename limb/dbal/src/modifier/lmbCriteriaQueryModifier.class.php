<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/modifier/lmbQueryModifier.interface.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');

class lmbCriteriaQueryModifier implements lmbQueryModifier
{
  protected $criteria;

  function __construct($criteria)
  {
    $this->criteria = $criteria;
  }

  function applyTo($query)
  {
    $query->addCriteria($this->criteria);
  }
}
?>