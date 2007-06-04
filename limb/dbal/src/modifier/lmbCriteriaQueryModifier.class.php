<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCriteriaQueryModifier.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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