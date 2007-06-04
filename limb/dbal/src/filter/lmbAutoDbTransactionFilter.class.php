<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbAutoDbTransactionFilter.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/dbal/src/drivers/lmbAutoTransactionConnection.class.php');

class lmbAutoDbTransactionFilter
{
  function run($chain)
  {
    $toolkit = lmbToolkit :: instance();
    $old_conn = $toolkit->getDefaultDbConnection();
    $conn = new lmbAutoTransactionConnection($old_conn);
    $toolkit->setDefaultDbConnection($conn);

    try
    {
      $chain->next();
      $conn->commitTransaction();
      $toolkit->setDefaultDbConnection($old_conn);
    }
    catch(Exception $e)
    {
      $conn->rollbackTransaction();
      $toolkit->setDefaultDbConnection($old_conn);
      throw $e;
    }
  }
}
?>
