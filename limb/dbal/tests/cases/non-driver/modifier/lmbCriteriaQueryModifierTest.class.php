<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/query/lmbSelectQuery.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
lmb_require('limb/dbal/src/modifier/lmbCriteriaQueryModifier.class.php');

Mock :: generate('lmbSelectQuery', 'MockSelectQuery');

class lmbCriteriaQueryModifierTest extends UnitTestCase
{
  function testApplyIsAddingCriteriaToQuery()
  {
    $query = new MockSelectQuery();

    $criteria = new lmbSQLFieldCriteria('some_field', 10);
    $modifier = new lmbCriteriaQueryModifier($criteria);

    $query->expectOnce('addCriteria', array($criteria));

    $modifier->applyTo($query);
  }
}
?>
