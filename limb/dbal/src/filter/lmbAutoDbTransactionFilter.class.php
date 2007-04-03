<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbAutoDbTransactionFilter.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
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
