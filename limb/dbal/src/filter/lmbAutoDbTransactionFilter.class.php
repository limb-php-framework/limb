<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbAutoTransactionConnection.class.php');

/**
 * class lmbAutoDbTransactionFilter.
 *
 * @package dbal
 * @version $Id: lmbAutoDbTransactionFilter.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
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

